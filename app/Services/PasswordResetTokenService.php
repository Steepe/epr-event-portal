<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

/**
 * Issues one-time, short-lived password setup/reset tokens for portal users.
 *
 * The browser receives the random token, while the database stores only its
 * SHA-256 digest. This table is already part of the portal schema and keeps
 * password setup links independent of third-party services.
 */
class PasswordResetTokenService
{
    private const TOKEN_TYPE = 'reset';
    private const EXPIRY_SECONDS = 3600;

    public function __construct(private readonly BaseConnection $db)
    {
    }

    public function issue(int $userId): string
    {
        $token = bin2hex(random_bytes(32));

        // A newer link deliberately invalidates earlier reset/setup links.
        $this->db->table('tbl_user_tokens')
            ->where('user_id', $userId)
            ->where('type', self::TOKEN_TYPE)
            ->delete();

        $this->db->table('tbl_user_tokens')->insert([
            'user_id' => $userId,
            'token' => $this->hash($token),
            'type' => self::TOKEN_TYPE,
            'expires_at' => date('Y-m-d H:i:s', time() + self::EXPIRY_SECONDS),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    public function valid(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        $record = $this->db->table('tbl_user_tokens')
            ->where('token', $this->hash($token))
            ->where('type', self::TOKEN_TYPE)
            ->get()
            ->getRowArray();

        if (! $record) {
            return null;
        }

        if (! empty($record['expires_at']) && strtotime($record['expires_at']) <= time()) {
            $this->db->table('tbl_user_tokens')->where('token_id', $record['token_id'])->delete();

            return null;
        }

        return $record;
    }

    public function consume(int $tokenId): void
    {
        $this->db->table('tbl_user_tokens')->where('token_id', $tokenId)->delete();
    }

    private function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
