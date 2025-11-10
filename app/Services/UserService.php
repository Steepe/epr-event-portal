<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 07:58
 */

namespace App\Services;

use Config\Services;

class UserService
{
    protected string $apiBase;
    protected string $apiKey;
    protected \CodeIgniter\HTTP\CURLRequest $client;

    public function __construct()
    {
        $this->apiBase = rtrim(base_url('api'), '/');
        $this->apiKey  = env('api.securityKey');
        $this->client  = Services::curlrequest([
            'http_errors' => false,
            'verify'      => false // bypass self-signed cert warning (development only)
        ]);
    }

    public function getUser(int $userId): ?array
    {
        $response = $this->client->get('https://eventportal.creyatif/api/users/' . $userId, [
            'verify' => false,        // 🔥 ignore SSL certificate
            'http_errors' => false,   // don't crash on 4xx/5xx
            'headers' => [
                'X-API-KEY' => getenv('API_KEY') ?: 'your_api_key_here'
            ],
        ]);

        if ($response->getStatusCode() !== 200) return null;

        $body = json_decode($response->getBody(), true);
        return $body['data'] ?? null;
    }

    public function getUserSessions(int $userId): array
    {
        $response = $this->client->get("{$this->apiBase}/users/{$userId}/sessions", [
            'headers' => ['X-API-KEY' => $this->apiKey]
        ]);

        if ($response->getStatusCode() !== 200) return [];

        $body = json_decode($response->getBody(), true);
        return $body['data'] ?? [];
    }
}
