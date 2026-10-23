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
        $this->url = (string) (getenv('supabase.url') ?: '');
        $this->serviceKey = (string) (getenv('supabase.service_key') ?: '');
    }

    public function insertMessage(int $sessionId, int $attendeeId, string $message): array
    {
        $payload = [
            "session_id" => $sessionId,
            "attendee_id" => $attendeeId,
            "message" => $message
        ];

        return $this->request('POST', 'tbl_session_chats', $payload, 'return=representation');
    }

    public function messages(int $sessionId, int $afterId = 0): array
    {
        $query = 'session_id=eq.' . rawurlencode((string) $sessionId) . '&order=id.asc';
        if ($afterId > 0) {
            $query .= '&id=gt.' . rawurlencode((string) $afterId);
        }

        return $this->request('GET', 'tbl_session_chats?' . $query);
    }

    private function request(string $method, string $path, ?array $payload = null, string $prefer = ''): array
    {
        if ($this->url === '' || $this->serviceKey === '') {
            return ['success' => false, 'data' => [], 'error' => 'Chat service is not configured.'];
        }

        $ch = curl_init(rtrim($this->url, '/') . "/rest/v1/{$path}");
        $headers = [
            'Content-Type: application/json',
            "apikey: {$this->serviceKey}",
            "Authorization: Bearer {$this->serviceKey}",
        ];
        if ($prefer !== '') {
            $headers[] = "Prefer: {$prefer}";
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $res = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $decoded = is_string($res) && $res !== '' ? json_decode($res, true) : [];

        return [
            'success' => $error === '' && $statusCode >= 200 && $statusCode < 300,
            'data' => is_array($decoded) ? $decoded : [],
            'error' => $error !== '' ? $error : ($statusCode >= 300 ? 'Chat service request failed.' : ''),
        ];
    }
}
