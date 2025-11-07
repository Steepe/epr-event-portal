<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 08:18
 */

namespace App\Services;

use Config\Services;

class MessageService
{
    protected string $apiBase;
    protected string $apiKey;
    protected $client;

    public function __construct()
    {
        $this->apiBase = rtrim(base_url('api'), '/');
        $this->apiKey  = env('api.securityKey');
        $this->client  = Services::curlrequest([
            'http_errors' => false,
            'verify'      => false
        ]);
    }

    public function getUnreadCount(int $userId): int
    {
        $response = $this->client->get("{$this->apiBase}/messages/unread/{$userId}", [
            'headers' => ['X-API-KEY' => $this->apiKey]
        ]);

        if ($response->getStatusCode() !== 200) return 0;

        $body = json_decode($response->getBody(), true);
        return $body['data']['unread_count'] ?? 0;
    }
}
