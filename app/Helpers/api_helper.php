<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 02:23
 */


use CodeIgniter\HTTP\ResponseInterface;

if (!function_exists('api_get')) {
    /**
     * Send a GET request to a protected API endpoint.
     *
     * @param string $endpoint Endpoint path (e.g., 'countries' or 'register/status')
     * @param array $query Optional query parameters
     * @param int $timeout Request timeout (seconds)
     *
     * @return array Decoded JSON response or standardized error array
     */
    function api_get(string $endpoint, array $query = [], int $timeout = 10): array
    {
        try {
            $client = service('curlrequest');

            // Build full API URL
            $url = rtrim(base_url('api/' . $endpoint), '/');

            // Load the security key
            $apiKey = env('api.securityKey');

            // Prepare request options
            $isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
            $options = [
                'headers' => ['X-API-KEY' => $apiKey],
                'query' => $query,
                'http_errors' => false,
                'timeout' => $timeout,
                'verify'      => !$isLocal, // Only verify SSL in production
            ];

            $response = $client->get($url, $options);
            $status = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($status >= 200 && $status < 300) {
                return [
                    'success' => true,
                    'status' => $status,
                    'data' => $body,
                ];
            }

            // Handle non-2xx responses gracefully
            return [
                'success' => false,
                'status' => $status,
                'error' => $body['message'] ?? 'API request failed',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'API GET failed [' . $endpoint . ']: ' . $e->getMessage());

            return [
                'success' => false,
                'status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'API call failed: ' . $e->getMessage(),
            ];
        }
    }
}
