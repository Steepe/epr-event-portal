<?php

namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblAttendeeConnectionsModel;
use App\Services\RealtimeNotifier;
use CodeIgniter\API\ResponseTrait;

class ConnectionsController extends BaseController
{
    use ResponseTrait;

    private TblAttendeeConnectionsModel $connections;

    public function __construct()
    {
        $this->connections = new TblAttendeeConnectionsModel();
    }

    public function create()
    {
        $currentUserId = $this->currentUserId();
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $targetUserId = (int) ($payload['receiver_id'] ?? 0);

        if ($currentUserId <= 0) {
            return $this->failUnauthorized('Please log in to send connection requests.');
        }

        if ($targetUserId <= 0) {
            return $this->failValidationErrors('Missing attendee to connect with.');
        }

        if ($targetUserId === $currentUserId) {
            return $this->failValidationErrors('You cannot connect with yourself.');
        }

        if (! $this->attendeeExists($targetUserId)) {
            return $this->failNotFound('Attendee not found.');
        }

        $existing = $this->findConnectionBetween($currentUserId, $targetUserId);
        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $connectionId = (int) $existing['id'];

            if ($existing['status'] === 'accepted') {
                return $this->respondConnection($existing, $currentUserId, 'Already connected.');
            }

            if ($existing['status'] === 'pending' && (int) $existing['receiver_id'] === $currentUserId) {
                $this->connections->update($connectionId, [
                    'status' => 'accepted',
                    'updated_at' => $now,
                    'accepted_at' => $now,
                ]);

                $accepted = $this->connections->find($connectionId);
                $this->notifyConnectionUpdated($accepted, 'accepted');
                return $this->respondConnection($accepted, $currentUserId, 'Connection accepted.');
            }

            if ($existing['status'] === 'pending') {
                return $this->respondConnection($existing, $currentUserId, 'Connection request already sent.');
            }

            $this->connections->update($connectionId, [
                'requester_id' => $currentUserId,
                'receiver_id' => $targetUserId,
                'status' => 'pending',
                'updated_at' => $now,
                'accepted_at' => null,
            ]);

            $resent = $this->connections->find($connectionId);
            $this->notifyConnectionRequest($resent);
            return $this->respondConnection($resent, $currentUserId, 'Connection request sent.');
        }

        $connectionId = $this->connections->insert([
            'requester_id' => $currentUserId,
            'receiver_id' => $targetUserId,
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ], true);

        $connection = $this->connections->find($connectionId);
        $this->notifyConnectionRequest($connection);
        return $this->respondConnection($connection, $currentUserId, 'Connection request sent.', 201);
    }

    public function accept($id = null)
    {
        return $this->setIncomingStatus((int) $id, 'accepted');
    }

    public function decline($id = null)
    {
        return $this->setIncomingStatus((int) $id, 'declined');
    }

    private function setIncomingStatus(int $connectionId, string $status)
    {
        $currentUserId = $this->currentUserId();
        if ($currentUserId <= 0) {
            return $this->failUnauthorized('Please log in to manage connection requests.');
        }

        $connection = $this->connections
            ->where('id', $connectionId)
            ->where('receiver_id', $currentUserId)
            ->first();

        if (! $connection) {
            return $this->failNotFound('Connection request not found.');
        }

        $now = date('Y-m-d H:i:s');
        $this->connections->update($connectionId, [
            'status' => $status,
            'updated_at' => $now,
            'accepted_at' => $status === 'accepted' ? $now : null,
        ]);

        $updated = $this->connections->find($connectionId);
        $this->notifyConnectionUpdated($updated, $status);
        return $this->respondConnection($updated, $currentUserId, $status === 'accepted' ? 'Connection accepted.' : 'Connection declined.');
    }

    private function findConnectionBetween(int $currentUserId, int $targetUserId): ?array
    {
        return $this->connections
            ->groupStart()
                ->where('requester_id', $currentUserId)
                ->where('receiver_id', $targetUserId)
            ->groupEnd()
            ->orGroupStart()
                ->where('requester_id', $targetUserId)
                ->where('receiver_id', $currentUserId)
            ->groupEnd()
            ->first();
    }

    private function attendeeExists(int $attendeeId): bool
    {
        return db_connect()
            ->table('tbl_attendees')
            ->where('attendee_id', $attendeeId)
            ->countAllResults() > 0;
    }

    private function respondConnection(array $connection, int $currentUserId, string $message, int $statusCode = 200)
    {
        $direction = (int) $connection['requester_id'] === $currentUserId ? 'sent' : 'received';

        return $this->respond([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'connection_id' => (int) $connection['id'],
                'connection_status' => $connection['status'],
                'connection_direction' => $direction,
                'is_connected' => $connection['status'] === 'accepted' ? 1 : 0,
            ],
        ], $statusCode);
    }

    private function currentUserId(): int
    {
        $userId = (int) session('user_id');
        if ($userId > 0) {
            return $userId;
        }

        return (int) session('attendee_id');
    }

    private function notifyConnectionRequest(?array $connection): void
    {
        if (! $connection || ($connection['status'] ?? '') !== 'pending') {
            return;
        }

        $event = $this->buildConnectionEvent($connection);
        if (! $event) {
            return;
        }

        $event['type'] = 'connection_request';
        $event['to'] = (int) $connection['receiver_id'];
        RealtimeNotifier::push($event);
    }

    private function notifyConnectionUpdated(?array $connection, string $status): void
    {
        if (! $connection) {
            return;
        }

        $event = $this->buildConnectionEvent($connection);
        if (! $event) {
            return;
        }

        $event['type'] = 'connection_update';
        $event['status'] = $status;
        $event['to'] = (int) $connection['requester_id'];
        RealtimeNotifier::push($event);
    }

    private function buildConnectionEvent(array $connection): ?array
    {
        $db = db_connect();
        $requester = $db->table('tbl_attendees a')
            ->select('a.firstname, a.lastname, a.company, a.position, u.email')
            ->join('tbl_users u', 'u.id = a.attendee_id', 'left')
            ->where('a.attendee_id', (int) $connection['requester_id'])
            ->get()
            ->getRowArray();

        if (! $requester) {
            return null;
        }

        $name = trim(ucfirst(strtolower((string) ($requester['firstname'] ?? ''))) . ' ' . ucfirst(strtolower((string) ($requester['lastname'] ?? ''))));

        return [
            'connection_id' => (int) $connection['id'],
            'requester_id' => (int) $connection['requester_id'],
            'receiver_id' => (int) $connection['receiver_id'],
            'status' => $connection['status'],
            'requester_name' => $name !== '' ? $name : ($requester['email'] ?? 'An attendee'),
            'requester_email' => (string) ($requester['email'] ?? ''),
            'requester_company' => (string) ($requester['company'] ?? ''),
            'requester_position' => (string) ($requester['position'] ?? ''),
            'created_at' => (string) ($connection['created_at'] ?? ''),
        ];
    }
}
