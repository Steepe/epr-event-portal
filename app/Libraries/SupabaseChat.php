<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 13/11/2025
 * Time: 16:53
 */

namespace App\Libraries;

class SupabaseChat
{
    private $url;
    private $serviceKey;

    public function __construct()
    {
        $this->url = getenv('supabase.url');
        $this->serviceKey = getenv('supabase.service_key');
    }

    public function insertMessage($sessionId, $attendeeId, $message)
    {
        $payload = [
            "session_id" => $sessionId,
            "attendee_id" => $attendeeId,
            "message" => $message
        ];

        $ch = curl_init("{$this->url}/rest/v1/tbl_session_chats");

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$this->serviceKey}",
                "Authorization: Bearer {$this->serviceKey}",
                "Prefer: return=minimal"
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $res = curl_exec($ch);
        return $res;
    }
}
