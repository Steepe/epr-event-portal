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
        $data = $this->request->getJSON(true);

        if (empty($data['sender_id']) || empty($data['receiver_id']) || empty($data['message'])) {
            return $this->failValidationErrors('Missing required fields.');
        }

        $msgId = $this->messages->insert([
            'sender_id'   => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'message'     => trim($data['message'])
        ]);

        return $this->respondCreated([
            'status' => 'success',
            'message_id' => $msgId
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
}
