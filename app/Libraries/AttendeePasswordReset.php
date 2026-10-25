<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Email\Email;
use InvalidArgumentException;
use RuntimeException;

final class AttendeePasswordReset
{
    public const SENT_MESSAGE = 'If an account exists for that email, a password reset link has been sent. Please check your inbox and spam folder.';

    public function __construct(private BaseConnection $db, private ?Email $mailer = null)
    {
    }

    public function sendLink(string $email): void
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }
        $user = $this->db->table('tbl_users')->where('email', $email)->get()->getRowArray();
        if (!$user) {
            return; // Do not disclose whether an address has an account.
        }

        $config = config('Email');
        if (!filter_var($config->fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Password reset email sender is not configured.');
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

        $link = site_url('attendees/reset-password') . '?token=' . rawurlencode($token);
        $mailer = $this->mailer ?? \Config\Services::email();
        try {
            $mailer->clear(true);
            $mailer->setFrom($config->fromEmail, $config->fromName ?: 'EPRGlobal Events');
            $mailer->setTo($user['email']);
            $mailer->setMailType('html');
            $mailer->setSubject('Password Reset Request - EPRGlobal Events');
            $mailer->setMessage('<p>Hello,</p><p>Use the link below to choose a new password for your EPRGlobal Events account.</p>'
                . '<p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">Reset my password</a></p>'
                . '<p>This link expires in one hour and can only be used once. If you did not request this, ignore this email.</p>');
            $mailer->setAltMessage("Reset your EPRGlobal Events password: {$link}\n\nThis link expires in one hour. If you did not request it, ignore this email.");
            if (!$mailer->send()) {
                throw new RuntimeException('Password reset email could not be sent.');
            }
        } catch (\Throwable $error) {
            $this->db->table('password_resets')->where('token', $tokenHash)->delete();
            throw $error;
        }
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
