<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 22:16
 */

namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblMessagesModel;
use CodeIgniter\API\ResponseTrait;

class MessagesController extends BaseController
{
    use ResponseTrait;
    protected $messages;

    public function __construct()
    {
        $this->messages = new TblMessagesModel();
    }

    public function send()
    {
        $data = $this->request->getJSON(true) ?: [];
        $senderId = $this->currentUserId();
        $receiverId = (int) ($data['receiver_id'] ?? 0);
        $message = trim((string) ($data['message'] ?? ''));

        if ($senderId <= 0) {
            return $this->failUnauthorized('Please log in to send messages.');
        }

        if ($receiverId <= 0 || $message === '') {
            return $this->failValidationErrors('Missing required fields.');
        }

        if ($receiverId === $senderId) {
            return $this->failValidationErrors('You cannot message yourself.');
        }

        if (mb_strlen($message) > 2000) {
            return $this->failValidationErrors('Messages cannot exceed 2,000 characters.');
        }

        $msgId = $this->messages->insert([
            'sender_id'   => $senderId,
            'receiver_id' => $receiverId,
            'message'     => $message,
            'is_read'     => 0,
        ]);

        $messageRecord = $this->messages->find($msgId);

        return $this->respondCreated([
            'status' => 'success',
            'data' => $messageRecord,
        ]);
    }

    public function thread($partnerId)
    {
        $currentUserId = $this->currentUserId();
        $partnerId = (int) $partnerId;
        if ($currentUserId <= 0) {
            return $this->failUnauthorized('Please log in to view messages.');
        }

        if ($partnerId <= 0 || $partnerId === $currentUserId) {
            return $this->failValidationErrors('Invalid conversation.');
        }

        $messages = $this->messages
            ->groupStart()
                ->where('sender_id', $currentUserId)
                ->where('receiver_id', $partnerId)
            ->groupEnd()
            ->orGroupStart()
                ->where('sender_id', $partnerId)
                ->where('receiver_id', $currentUserId)
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->findAll();

        $this->messages
            ->where('sender_id', $partnerId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', 0)
            ->set('is_read', 1)
            ->update();

        return $this->respond([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    public function inbox($userId)
    {
        return $this->respond([
            'status' => 'success',
            'data'   => $this->messages->where('receiver_id', $userId)->orderBy('id', 'DESC')->findAll()
        ]);
    }

    public function outbox($userId)
    {
        return $this->respond([
            'status' => 'success',
            'data'   => $this->messages->where('sender_id', $userId)->orderBy('id', 'DESC')->findAll()
        ]);
    }

    private function currentUserId(): int
    {
        $userId = (int) session('user_id');
        return $userId > 0 ? $userId : (int) session('attendee_id');
    }
}
