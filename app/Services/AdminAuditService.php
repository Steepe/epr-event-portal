<?php

namespace App\Services;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuditService
{
    /** @var list<string> */
    private const SENSITIVE_FIELDS = ['password', 'password_confirm', 'token', 'csrf', 'secret', 'api_key', 'accesskey'];

    public function record(string $event, RequestInterface $request, ?ResponseInterface $response = null, array $context = []): void
    {
        try {
            $session = session();
            $route = '/' . ltrim($request->getUri()->getPath(), '/');
            [$resourceType, $resourceId] = $this->resourceFromRoute($route);

            db_connect()->table('tbl_admin_activity_logs')->insert([
                'admin_id' => $context['admin_id'] ?? $session->get('admin_id'),
                'admin_name' => $context['admin_name'] ?? $session->get('admin_name'),
                'admin_email' => $context['admin_email'] ?? $session->get('admin_email'),
                'event' => $event,
                'http_method' => $request->getMethod(),
                'route' => $route,
                'resource_type' => $context['resource_type'] ?? $resourceType,
                'resource_id' => $context['resource_id'] ?? $resourceId,
                'details' => $this->detailsJson($request, $context['details'] ?? []),
                'response_status' => $response?->getStatusCode(),
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $this->userAgent($request),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $exception) {
            // Audit capture must never interrupt an administrator's work.
            log_message('error', 'Admin audit logging failed: ' . $exception->getMessage());
        }
    }

    public function eventFor(RequestInterface $request): string
    {
        $path = strtolower('/' . ltrim($request->getUri()->getPath(), '/'));

        foreach (['/delete' => 'delete', '/toggle' => 'toggle', '/export' => 'export', '/reset' => 'reset_password', '/store' => 'create', '/update' => 'update'] as $needle => $event) {
            if (str_contains($path, $needle)) {
                return $event;
            }
        }

        return strtoupper($request->getMethod()) === 'GET' ? 'view' : 'submit';
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function resourceFromRoute(string $route): array
    {
        $segments = array_values(array_filter(explode('/', trim($route, '/'))));
        if (($segments[0] ?? null) === 'admin') {
            array_shift($segments);
        }

        $resourceType = $segments[0] ?? null;
        $resourceId = null;
        foreach (array_reverse($segments) as $segment) {
            if (ctype_digit($segment)) {
                $resourceId = $segment;
                break;
            }
        }

        return [$resourceType, $resourceId];
    }

    private function detailsJson(RequestInterface $request, array $context): string
    {
        $details = [
            'query' => $this->sanitize($request->getGet()),
            'input' => $this->sanitize($request->getPost()),
            'files' => $this->fileDetails($request->getFiles()),
        ];

        if ($context !== []) {
            $details['context'] = $this->sanitize($context);
        }

        $json = json_encode($details, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

        return substr($json === false ? '{}' : $json, 0, 16000);
    }

    private function sanitize(mixed $value, string $field = ''): mixed
    {
        $normalised = strtolower(str_replace(['-', '_'], '', $field));
        foreach (self::SENSITIVE_FIELDS as $sensitive) {
            if (str_contains($normalised, str_replace('_', '', $sensitive))) {
                return '[REDACTED]';
            }
        }

        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $item) {
                $clean[(string) $key] = $this->sanitize($item, (string) $key);
            }

            return $clean;
        }

        if (is_string($value)) {
            return mb_substr($value, 0, 2000);
        }

        return is_scalar($value) || $value === null ? $value : get_debug_type($value);
    }

    private function fileDetails(array $files): array
    {
        $details = [];
        foreach ($files as $field => $file) {
            if (is_array($file)) {
                $details[$field] = $this->fileDetails($file);
                continue;
            }

            if ($file instanceof \CodeIgniter\HTTP\Files\UploadedFile && $file->getError() !== UPLOAD_ERR_NO_FILE) {
                $details[$field] = [
                    'name' => $file->getClientName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        return $details;
    }

    private function userAgent(RequestInterface $request): ?string
    {
        if (! method_exists($request, 'getUserAgent')) {
            return null;
        }

        return substr($request->getUserAgent()->getAgentString(), 0, 1000);
    }
}
