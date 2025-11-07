<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 07/11/2025
 * Time: 08:23
 */


namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class ApiAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();

        // ✅ Allow logged-in attendees via session
        if ($session->has('attendee_id') && !empty($session->get('attendee_id'))) {
            return;
        }

        // ✅ Allow API access via X-API-KEY header
        $apiKey = $request->getHeaderLine('X-API-KEY');
        if (!empty($apiKey) && $this->isValidApiKey($apiKey)) {
            return;
        }

        // ❌ Otherwise reject
        return Services::response()
            ->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized. Please log in or provide a valid API key.'
            ])
            ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing
    }

    private function isValidApiKey(string $key): bool
    {
        // 🔐 Read from .env
        $validApiKey = env('api.securityKey', 'EPRGLOBAL-SECURE-2025');
        return hash_equals($validApiKey, trim($key));
    }
}
