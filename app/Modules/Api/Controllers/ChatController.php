<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 13/11/2025
 * Time: 16:57
 */

namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\BaseConnection;

class ChatController extends BaseController
{
    use ResponseTrait;

    public function send($sessionId)
    {
        $sessionId = (int) $sessionId;
        $data = $this->request->getJSON(true) ?: [];
        $message = trim((string) ($data['message'] ?? ''));
        $attendeeId = (int) session('attendee_id');

        if ($sessionId <= 0 || $attendeeId <= 0) {
            return $this->failUnauthorized('Please log in to use live chat.');
        }

        if ($message === '') {
            return $this->failValidationErrors('A chat message is required.');
        }

        if (mb_strlen($message) > 2000) {
            return $this->failValidationErrors('Chat messages cannot exceed 2,000 characters.');
        }

        try {
            $db = db_connect();
            $this->ensureChatTable($db);
            $db->table('tbl_session_chats')->insert([
                'session_id' => $sessionId,
                'attendee_id' => $attendeeId,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $chatMessage = $db->table('tbl_session_chats')
                ->where('id', $db->insertID())
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', '[ChatController] Unable to send chat message: ' . $e->getMessage());
            return $this->failServerError('Unable to send chat message. Please try again.');
        }

        return $this->respondCreated([
            'status' => 'success',
            'data' => $this->withAuthorNames($chatMessage),
        ]);
    }

    public function index($sessionId)
    {
        $sessionId = (int) $sessionId;
        if ($sessionId <= 0) {
            return $this->failValidationErrors('Invalid session.');
        }

        try {
            $db = db_connect();
            $this->ensureChatTable($db);
            $messages = $db->table('tbl_session_chats')
                ->where('session_id', $sessionId)
                ->where('id >', max(0, (int) $this->request->getGet('after')))
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', '[ChatController] Unable to load chat messages: ' . $e->getMessage());
            return $this->failServerError('Unable to load chat messages.');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $this->withAuthorNames($messages),
        ]);
    }

    private function ensureChatTable(BaseConnection $db): void
    {
        if ($db->tableExists('tbl_session_chats')) {
            return;
        }

        $forge = \Config\Database::forge($db);
        $forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'session_id' => ['type' => 'INT', 'unsigned' => true],
            'attendee_id' => ['type' => 'INT', 'unsigned' => true],
            'message' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $forge->addKey('id', true);
        $forge->addKey(['session_id', 'id']);
        $forge->createTable('tbl_session_chats', true);
    }

    private function withAuthorNames(array $messages): array
    {
        $attendeeIds = array_values(array_unique(array_filter(array_map(
            static fn ($message): int => (int) ($message['attendee_id'] ?? 0),
            $messages
        ))));
        if ($attendeeIds === []) {
            return $messages;
        }

        $attendees = db_connect()->table('tbl_attendees')
            ->select('id, firstname, lastname')
            ->whereIn('id', $attendeeIds)
            ->get()
            ->getResultArray();
        $names = [];
        foreach ($attendees as $attendee) {
            $names[(int) $attendee['id']] = trim(($attendee['firstname'] ?? '') . ' ' . ($attendee['lastname'] ?? ''));
        }

        foreach ($messages as &$message) {
            $message['attendee_name'] = $names[(int) ($message['attendee_id'] ?? 0)] ?? 'Attendee';
        }
        unset($message);

        return $messages;
    }
}
