<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 00:16
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblAttendeesModel;
use App\Modules\Api\Models\TblUsersModel;

class AttendeesController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblAttendeesModel::class;
    protected string $format    = 'json';

    /**
     * GET /api/attendees
     */
    public function index()
    {
        try {
            $name = $this->request->getGet('attendee_name');
            $sort = $this->request->getGet('sort');
            $db = db_connect();
            $hasStatusTable = $db->tableExists('tbl_user_status');

            $builder = $this->model
                ->select("
        tbl_attendees.id,
        tbl_attendees.attendee_id,
        tbl_attendees.firstname,
        tbl_attendees.lastname,
        tbl_attendees.company,
        tbl_attendees.position,
        tbl_attendees.country,
        tbl_attendees.state,
        tbl_attendees.city,
        tbl_attendees.profile_picture,
        tbl_attendees.facebook,
        tbl_attendees.twitter,
        tbl_attendees.instagram,
        tbl_attendees.linkedin,
        tbl_users.email
    ")
                ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id');

            if ($hasStatusTable) {
                $builder
                    ->select("
                        CASE
                            WHEN tbl_user_status.is_online = 1
                                AND tbl_user_status.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                            THEN 1
                            ELSE 0
                        END AS is_online,
                        tbl_user_status.last_seen
                    ", false)
                    ->join('tbl_user_status', 'tbl_user_status.user_id = tbl_attendees.attendee_id', 'left');
            } else {
                $builder->select('0 AS is_online, NULL AS last_seen', false);
            }

            if ($name) {
                $builder->groupStart()
                    ->like('tbl_attendees.firstname', $name)
                    ->orLike('tbl_attendees.lastname', $name)
                    ->orLike("CONCAT(tbl_attendees.firstname, ' ', tbl_attendees.lastname)", $name, 'both', false)
                    ->orLike('tbl_attendees.company', $name)
                    ->orLike('tbl_attendees.position', $name)
                    ->groupEnd();
            }

            if ($sort) {
                $builder->like('tbl_attendees.firstname', $sort, 'after');
            }

            $attendees = $builder
                ->orderBy('tbl_attendees.firstname', 'ASC')
                ->findAll();
            $attendees = $this->applyRedisOnlineState($attendees);
            $attendees = $this->applyConnectionState($attendees);

            // Generate A–Z letters
            $alphabetCount = [];
            foreach (range('A', 'Z') as $letter) {
                $alphabetCount[] = ['firstCharacter' => $letter];
            }

            return $this->respond([
                'status'  => 200,
                'message' => 'Attendees retrieved successfully.',
                'data'    => [
                    'attendees'      => $attendees,
                    'alphabet_count' => $alphabetCount,
                ],
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[API:AttendeesController] '.$e->getMessage());
            return $this->failServerError('Unable to fetch attendees.');
        }
    }

    /**
     * GET /api/attendees/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->failValidationError('Missing attendee ID.');
        }
        $db = db_connect();
        $hasStatusTable = $db->tableExists('tbl_user_status');

        $attendee = $this->model
            ->select("
                tbl_attendees.id,
                tbl_attendees.attendee_id,
                tbl_attendees.firstname,
                tbl_attendees.lastname,
                tbl_attendees.company,
                tbl_attendees.position,
                tbl_attendees.country,
                tbl_attendees.state,
                tbl_attendees.city,
                tbl_attendees.profile_picture,
                tbl_attendees.facebook,
                tbl_attendees.twitter,
                tbl_attendees.instagram,
                tbl_attendees.linkedin,
                tbl_users.email
            ")
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id');

        if ($hasStatusTable) {
            $attendee
                ->select("
                    CASE
                        WHEN tbl_user_status.is_online = 1
                            AND tbl_user_status.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                        THEN 1
                        ELSE 0
                    END AS is_online,
                    tbl_user_status.last_seen
                ", false)
                ->join('tbl_user_status', 'tbl_user_status.user_id = tbl_attendees.attendee_id', 'left');
        } else {
            $attendee->select('0 AS is_online, NULL AS last_seen', false);
        }

        $attendee = $attendee->find($id);

        if (!$attendee) {
            return $this->failNotFound('Attendee not found.');
        }

        $attendee = $this->applyRedisOnlineState([$attendee])[0] ?? $attendee;
        $attendee = $this->applyConnectionState([$attendee])[0] ?? $attendee;

        return $this->respond([
            'status' => 200,
            'data'   => $attendee,
        ]);
    }

    /**
     * GET /api/attendees/all (requires X-API-KEY)
     */
    public function all()
    {
        $key = $this->request->getHeaderLine('X-API-KEY');
        if ($key !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API key');
        }

        $rows = $this->model
            ->select("
                tbl_attendees.id,
                tbl_attendees.firstname,
                tbl_attendees.lastname
            ")
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $rows,
        ]);
    }

    /**
     * Prefer the live websocket/Redis status when it is available.
     *
     * The database status is still selected above as a fallback, but Redis is
     * the source that the chat server updates when someone is actually online.
     */
    private function applyRedisOnlineState(array $attendees): array
    {
        if (empty($attendees) || ! class_exists(\Redis::class)) {
            return $attendees;
        }

        $keysByIndex = [];
        foreach ($attendees as $index => $attendee) {
            $attendeeId = (int) ($attendee['attendee_id'] ?? 0);
            if ($attendeeId > 0) {
                $keysByIndex[$index] = "user:{$attendeeId}:online";
            }
        }

        if (empty($keysByIndex)) {
            return $attendees;
        }

        try {
            $redis = new \Redis();
            $redis->connect(env('redis.host') ?: '127.0.0.1', (int) (env('redis.port') ?: 6379), 0.2);
            $statuses = $redis->mGet(array_values($keysByIndex));
            $redis->close();
        } catch (\Throwable $e) {
            log_message('debug', '[API:AttendeesController] Redis status unavailable: '.$e->getMessage());
            return $attendees;
        }

        $offset = 0;
        foreach ($keysByIndex as $index => $key) {
            $attendees[$index]['is_online'] = ! empty($statuses[$offset]) && (string) $statuses[$offset] === '1' ? 1 : 0;
            $offset++;
        }

        return $attendees;
    }

    private function applyConnectionState(array $attendees): array
    {
        $currentUserId = $this->resolveCurrentAttendeeId();

        foreach ($attendees as $index => $attendee) {
            $attendees[$index]['is_connected'] = 0;
            $attendees[$index]['connection_id'] = null;
            $attendees[$index]['connection_status'] = null;
            $attendees[$index]['connection_direction'] = null;
        }

        if ($currentUserId <= 0 || empty($attendees)) {
            return $attendees;
        }

        $db = db_connect();
        if (! $db->tableExists('tbl_attendee_connections')) {
            return $attendees;
        }

        $attendeeIds = [];
        foreach ($attendees as $attendee) {
            $attendeeId = (int) ($attendee['attendee_id'] ?? 0);
            if ($attendeeId > 0 && $attendeeId !== $currentUserId) {
                $attendeeIds[] = $attendeeId;
            }
        }

        $attendeeIds = array_values(array_unique($attendeeIds));
        if (empty($attendeeIds)) {
            return $attendees;
        }

        $connections = $db->table('tbl_attendee_connections')
            ->groupStart()
                ->where('requester_id', $currentUserId)
                ->whereIn('receiver_id', $attendeeIds)
            ->groupEnd()
            ->orGroupStart()
                ->where('receiver_id', $currentUserId)
                ->whereIn('requester_id', $attendeeIds)
            ->groupEnd()
            ->get()
            ->getResultArray();

        $connectionsByAttendee = [];
        foreach ($connections as $connection) {
            $requesterId = (int) $connection['requester_id'];
            $receiverId = (int) $connection['receiver_id'];
            $otherId = $requesterId === $currentUserId ? $receiverId : $requesterId;
            $connectionsByAttendee[$otherId] = $connection;
        }

        foreach ($attendees as $index => $attendee) {
            $attendeeId = (int) ($attendee['attendee_id'] ?? 0);
            if (! isset($connectionsByAttendee[$attendeeId])) {
                continue;
            }

            $connection = $connectionsByAttendee[$attendeeId];
            $attendees[$index]['connection_id'] = (int) $connection['id'];
            $attendees[$index]['connection_status'] = $connection['status'];
            $attendees[$index]['connection_direction'] = (int) $connection['requester_id'] === $currentUserId ? 'sent' : 'received';
            $attendees[$index]['is_connected'] = $connection['status'] === 'accepted' ? 1 : 0;
        }

        return $attendees;
    }

    private function resolveCurrentAttendeeId(): int
    {
        $sessionUserId = (int) session('user_id');
        if ($sessionUserId > 0) {
            return $sessionUserId;
        }

        $queryUserId = (int) $this->request->getGet('current_user_id');
        if ($queryUserId > 0) {
            return $queryUserId;
        }

        return (int) $this->request->getGet('current_attendee_id');
    }
}
