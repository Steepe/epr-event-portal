<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Email\Email;
use InvalidArgumentException;
use RuntimeException;

final class AttendeePasswordReset
{
    public const SENT_MESSAGE = 'A password reset link has been sent. Please check your inbox and spam folder.';
    public const ACCOUNT_NOT_FOUND_MESSAGE = 'We could not find an account with that email address. Please check the address or create an account.';

    public function __construct(private BaseConnection $db, private ?Email $mailer = null)
    {
    }

    /**
     * Sends a reset link and reports whether an attendee account was found.
     *
     * The caller intentionally uses this result to give the portal user a
     * clear registration path when their email address has no account.
     */
    public function sendLink(string $email): bool
    {
        return $this->sendEmail($email, false);
    }

    /**
     * Sends the administrator-initiated password-creation email.
     *
     * Any previous reset links are invalidated first, so an administrator can
     * be confident that this is the only active password-creation link.
     */
    public function sendAdminResetLink(string $email): bool
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The attendee does not have a valid email address.');
        }

        $this->db->table('password_resets')->where('email', $email)->delete();

        return $this->sendEmail($email, true);
    }

    private function sendEmail(string $email, bool $adminInitiated): bool
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }
        $config = config('Email');
        if (!filter_var($config->fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Password reset email sender is not configured.');
        }
        $issued = $this->issueLink($email);
        if ($issued === null) {
            return false;
        }

        $mailer = $this->mailer ?? \Config\Services::email();
        try {
            $mailer->clear(true);
            $mailer->setFrom($config->fromEmail, $config->fromName ?: 'EPRGlobal Events');
            $mailer->setTo($issued['email']);
            $mailer->setMailType('html');
            $mailer->setSubject($adminInitiated ? 'Set a new password for your EPR Global Events account' : 'Password Reset Request - EPRGlobal Events');
            $mailer->setMessage(view('emails/attendee_password_reset', [
                'resetLink' => $issued['link'],
                'supportEmail' => 'registration@eprglobal.com',
                'adminInitiated' => $adminInitiated,
            ]));
            $mailer->setAltMessage(($adminInitiated ? 'An EPR Global administrator has reset your portal password. Set a new password: ' : 'Reset your EPRGlobal Events password: ') . "{$issued['link']}\n\nThis link expires in one hour and can only be used once.");
            if (!$mailer->send()) {
                throw new RuntimeException('Password reset email could not be sent.');
            }
            return true;
        } catch (\Throwable $error) {
            $this->db->table('password_resets')->where('token', $issued['token_hash'])->delete();
            throw $error;
        }
    }

    /** Creates a one-time password setup URL without sending a second email. */
    public function issueLinkForEmail(string $email): ?string
    {
        $issued = $this->issueLink($email);

        return $issued['link'] ?? null;
    }

    /** @return array{email:string, link:string, token_hash:string}|null */
    private function issueLink(string $email): ?array
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }
        $user = $this->db->table('tbl_users')->where('email', $email)->get()->getRowArray();
        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $now = time();
        if (!$this->db->table('password_resets')->insert([
            'email' => $user['email'],
            'token' => $tokenHash,
            'created_at' => date('Y-m-d H:i:s', $now),
            'expires_at' => date('Y-m-d H:i:s', $now + 3600),
        ])) {
            throw new RuntimeException('Could not create password reset request.');
        }

        return [
            'email' => (string) $user['email'],
            'link' => site_url('attendees/reset-password') . '?token=' . rawurlencode($token),
            'token_hash' => $tokenHash,
        ];
    }

    public function isValid(string $token): bool
    {
        return $this->findValid($token) !== null;
    }

    private function findValid(string $token): ?array
    {
        if (!preg_match('/\A[a-f0-9]{64}\z/D', $token)) {
            return null;
        }
        // Accept old un-hashed links until they naturally expire.
        $record = $this->db->table('password_resets')
            ->whereIn('token', [hash('sha256', $token), $token])->get()->getRowArray();
        if (!$record) {
            return null;
        }
        $expires = !empty($record['expires_at']) ? strtotime($record['expires_at']) : false;
        $created = !empty($record['created_at']) ? strtotime($record['created_at']) : false;
        if ($created !== false) {
            $expires = $expires === false ? $created + 3600 : min($expires, $created + 3600);
        }
        return $expires !== false && $expires > time() ? $record : null;
    }

    public function reset(string $token, string $password, string $confirmation): void
    {
        if (strlen($password) < 8 || strlen($password) > 72) {
            throw new InvalidArgumentException('Use a password between 8 and 72 bytes long.');
        }
        if ($password !== $confirmation) {
            throw new InvalidArgumentException('Passwords do not match.');
        }
        $record = $this->findValid($token);
        if (!$record) {
            throw new InvalidArgumentException('This reset link is invalid or has expired. Please request a new one.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->transBegin();
        try {
            // Claim the token atomically: a concurrent/repeated request cannot reuse it.
            $this->db->table('password_resets')->where('token', $record['token'])->delete();
            if ($this->db->affectedRows() !== 1) {
                throw new InvalidArgumentException('This reset link has already been used. Please request a new one.');
            }
            $this->db->table('tbl_users')->where('email', $record['email'])
                ->update(['password' => $hash, 'updated_at' => date('Y-m-d H:i:s')]);
            if ($this->db->affectedRows() !== 1) {
                throw new RuntimeException('Could not update the account password.');
            }
            $this->db->table('password_resets')->where('email', $record['email'])->delete();
            if (!$this->db->transStatus()) {
                throw new RuntimeException('Could not save the password reset.');
            }
            $this->db->transCommit();
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }
}
