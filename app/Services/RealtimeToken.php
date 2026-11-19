<?php

namespace App\Services;

/**
 * Signs the short-lived identity assertion used by the standalone WebSocket
 * process. The socket server cannot read CodeIgniter's PHP session directly,
 * so it verifies this assertion before accepting a browser registration.
 */
final class RealtimeToken
{
    /** @return array{expires: int, token: string}|null */
    public static function issue(int $userId, int $ttl = 7200): ?array
    {
        $secret = self::secret();
        if ($userId <= 0 || $secret === '') {
            return null;
        }

        $expires = time() + max(60, $ttl);

        return [
            'expires' => $expires,
            'token' => hash_hmac('sha256', $userId . '|' . $expires, $secret),
        ];
    }

    public static function verify(int $userId, int $expires, string $token): bool
    {
        $secret = self::secret();
        if ($userId <= 0 || $expires < time() || $token === '' || $secret === '') {
            return false;
        }

        return hash_equals(
            hash_hmac('sha256', $userId . '|' . $expires, $secret),
            $token
        );
    }

    private static function secret(): string
    {
        return trim((string) (env('websocket.secret') ?: env('WEBSOCKET_SECRET') ?: ''));
    }
}
