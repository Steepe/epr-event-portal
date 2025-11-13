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
        $data = $this->request->getJSON(true);

        $chat = new SupabaseChat();
        $chat->insertMessage(
            $sessionId,
            $data['attendee_id'],
            $data['message']
        );

        return $this->respond([
            'status' => 'ok'
        ]);
    }
}
