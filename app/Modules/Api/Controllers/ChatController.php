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
use App\Libraries\SupabaseChat;
use CodeIgniter\API\ResponseTrait;

class ChatController extends BaseController
{
    use ResponseTrait;

    public function send($sessionId)
    {
        $data = $this->request->getJSON(true) ?: [];
        $attendeeId = (int) (session('user_id') ?: session('attendee_id'));
        if ($attendeeId <= 0) {
            return $this->failUnauthorized('Please sign in to use live chat.');
        }

        $chat = new SupabaseChat();
        $response = $chat->insertMessage(
            $sessionId,
            $attendeeId,
            (string) ($data['message'] ?? '')
        );

        return $this->respond([
            'status' => $response,
        ]);
    }
}
