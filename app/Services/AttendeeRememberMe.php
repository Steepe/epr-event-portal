<?php

namespace App\Services;

/**
 * Keeps an attendee signed in on a trusted device without ever storing their
 * password in a browser cookie. The database stores only a SHA-256 digest of
 * the random device token, so a database export cannot be used as a login.
 */
final class AttendeeRememberMe
{
    private const COOKIE = 'epr_remember';
    private const TOKEN_TYPE = 'api';
    private const PURPOSE = 'remember_me';
    private const LIFETIME = 1_296_000; // 15 days

    public function issue(array $user, array $attendee): void
    {
        $userId = (int) ($user['id'] ?? 0);
        if ($userId < 1 || ! $this->tokensReady()) {
            return;
        }

        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $db = db_connect();
        $this->revokeAllForUser($userId);
        $db->table('tbl_user_tokens')->insert([
            'user_id' => $userId,
            'token' => hash('sha256', $token),
            'type' => self::TOKEN_TYPE,
            'expires_at' => gmdate('Y-m-d H:i:s', time() + self::LIFETIME),
            'meta' => json_encode(['purpose' => self::PURPOSE]),
        ]);

        $this->setCookie($token);
    }

    /** Rehydrates the normal attendee session from a valid remembered device. */
    public function restore(): bool
    {
        if (session()->has('attendee_id')) {
            return true;
        }

        $token = (string) service('request')->getCookie(self::COOKIE);
        if ($token === '' || ! $this->tokensReady()) {
            return false;
        }

        $db = db_connect();
        $record = $db->table('tbl_user_tokens t')
            ->select('t.token_id, t.user_id, t.expires_at, u.id, u.uuid, u.email, u.role, a.id AS attendee_row_id, a.firstname, a.lastname, a.country, a.profile_picture')
            ->join('tbl_users u', 'u.id = t.user_id', 'inner')
            ->join('tbl_attendees a', 'a.attendee_id = u.id', 'inner')
            ->where('t.token', hash('sha256', $token))
            ->where('t.type', self::TOKEN_TYPE)
            ->like('t.meta', '"purpose":"' . self::PURPOSE . '"')
            ->get()
            ->getRowArray();

        if (! $record || strtotime((string) $record['expires_at']) <= time()) {
            if ($record) {
                $db->table('tbl_user_tokens')->where('token_id', (int) $record['token_id'])->delete();
            }
            $this->clearCookie();
            return false;
        }

        session()->regenerate(true);
        session()->set($this->sessionData($record));
        $db->table('tbl_user_tokens')->where('token_id', (int) $record['token_id'])->update([
            'expires_at' => gmdate('Y-m-d H:i:s', time() + self::LIFETIME),
        ]);
        $this->setCookie($token);

        return true;
    }

    public function clearCurrentDevice(): void
    {
        $token = (string) service('request')->getCookie(self::COOKIE);
        if ($token !== '' && $this->tokensReady()) {
            db_connect()->table('tbl_user_tokens')
                ->where('token', hash('sha256', $token))
                ->where('type', self::TOKEN_TYPE)
                ->like('meta', '"purpose":"' . self::PURPOSE . '"')
                ->delete();
        }
        $this->clearCookie();
    }

    public function revokeAllForUser(int $userId): void
    {
        if ($userId < 1 || ! $this->tokensReady()) {
            return;
        }

        db_connect()->table('tbl_user_tokens')
            ->where('user_id', $userId)
            ->where('type', self::TOKEN_TYPE)
            ->like('meta', '"purpose":"' . self::PURPOSE . '"')
            ->delete();
    }

    private function sessionData(array $record): array
    {
        return [
            'logged_in' => true,
            'isLoggedIn' => true,
            'user_id' => (int) $record['id'],
            'attendee_id' => (int) $record['id'],
            'attendee_row_id' => (int) $record['attendee_row_id'],
            'uuid' => $record['uuid'] ?? null,
            'email' => $record['email'],
            'user_email' => $record['email'],
            'role' => $record['role'],
            'user_role' => $record['role'],
            'firstname' => $record['firstname'] ?? '',
            'lastname' => $record['lastname'] ?? '',
            'fullname' => trim((string) ($record['firstname'] ?? '') . ' ' . (string) ($record['lastname'] ?? '')),
            'reg_country' => $record['country'] ?? '',
            'profile_pic' => $record['profile_picture'] ?? null,
            'profile_picture' => $record['profile_picture'] ?? null,
        ];
    }

    private function tokensReady(): bool
    {
        try {
            return db_connect()->tableExists('tbl_user_tokens');
        } catch (\Throwable) {
            return false;
        }
    }

    private function setCookie(string $token): void
    {
        service('response')->setCookie(self::COOKIE, $token, self::LIFETIME, '', '/', '', service('request')->isSecure(), true, 'Lax');
    }

    private function clearCookie(): void
    {
        service('response')->deleteCookie(self::COOKIE, '', '/');
    }
}
