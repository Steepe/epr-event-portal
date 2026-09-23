<?php

namespace App\Services;

class MailchimpService
{
    public function syncEmergenceContact(array $payload): array
    {
        $apiKey = (string) (env('mailchimp.apiKey') ?: env('MAILCHIMP_API_KEY'));
        $audienceId = (string) (env('mailchimp.audienceId') ?: env('mailchimp.listId') ?: env('MAILCHIMP_AUDIENCE_ID'));
        $serverPrefix = (string) (env('mailchimp.serverPrefix') ?: env('MAILCHIMP_SERVER_PREFIX'));

        if ($serverPrefix === '' && str_contains($apiKey, '-')) {
            $serverPrefix = substr(strrchr($apiKey, '-'), 1) ?: '';
        }

        if ($apiKey === '' || $audienceId === '' || $serverPrefix === '') {
            return [
                'status' => 'skipped',
                'detail' => 'Mailchimp is not configured.',
            ];
        }

        $email = strtolower(trim((string) $payload['email']));
        $subscriberHash = md5($email);
        $baseUrl = 'https://' . $serverPrefix . '.api.mailchimp.com/3.0';
        $tags = $this->normaliseTags($payload['mailchimp']['tags'] ?? []);
        $statusIfNew = $payload['mailchimp']['status_if_new'] ?? null;

        $memberPayload = [
            'email_address' => $email,
            'status_if_new' => in_array($statusIfNew, ['subscribed', 'pending', 'transactional'], true)
                ? $statusIfNew
                : 'transactional',
            'merge_fields' => $this->mergeFields($payload),
        ];

        $member = $this->request(
            'PUT',
            $baseUrl . '/lists/' . rawurlencode($audienceId) . '/members/' . $subscriberHash,
            $memberPayload,
            $apiKey
        );

        if (! $member['ok']) {
            return [
                'status' => 'failed',
                'detail' => $member,
            ];
        }

        $tagResult = null;
        if ($tags !== []) {
            $tagPayload = [
                'tags' => array_map(
                    static fn (string $tag): array => ['name' => $tag, 'status' => 'active'],
                    $tags
                ),
            ];

            $tagResult = $this->request(
                'POST',
                $baseUrl . '/lists/' . rawurlencode($audienceId) . '/members/' . $subscriberHash . '/tags',
                $tagPayload,
                $apiKey
            );
        }

        return [
            'status' => 'ok',
            'detail' => [
                'member' => [
                    'http_code' => $member['http_code'],
                    'status' => $member['body']['status'] ?? null,
                    'id' => $member['body']['id'] ?? null,
                ],
                'tags' => $tagResult === null ? null : [
                    'http_code' => $tagResult['http_code'],
                    'ok' => $tagResult['ok'],
                    'body' => $tagResult['body'],
                ],
            ],
        ];
    }

    private function mergeFields(array $payload): array
    {
        $fields = [
            'FNAME' => (string) ($payload['first_name'] ?? ''),
            'LNAME' => (string) ($payload['last_name'] ?? ''),
        ];

        $configuredFields = (string) env('mailchimp.mergeFields', '');
        foreach (array_filter(array_map('trim', explode(',', $configuredFields))) as $mapping) {
            [$mergeTag, $payloadKey] = array_pad(array_map('trim', explode(':', $mapping, 2)), 2, '');
            if ($mergeTag === '' || $payloadKey === '' || ! array_key_exists($payloadKey, $payload)) {
                continue;
            }

            $value = $payload[$payloadKey];
            if ($value !== null && $value !== '') {
                $fields[strtoupper($mergeTag)] = (string) $value;
            }
        }

        return $fields;
    }

    private function normaliseTags(array|string $tags): array
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        $tags = array_map(
            static fn ($tag): string => trim((string) $tag),
            $tags
        );

        return array_values(array_unique(array_filter($tags)));
    }

    private function request(string $method, string $url, array $payload, string $apiKey): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_USERPWD => 'apikey:' . $apiKey,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 12,
        ]);

        $rawBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $body = is_string($rawBody) && $rawBody !== ''
            ? json_decode($rawBody, true)
            : null;

        return [
            'ok' => $curlError === '' && $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'body' => $body,
            'error' => $curlError ?: null,
        ];
    }
}
