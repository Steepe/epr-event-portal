<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:59
 */


namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblUsersModel;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    protected TblUsersModel $users;
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->users = new TblUsersModel();
        $this->db = \Config\Database::connect(); // ✅ FIX: Initialize DB connection
    }

    // POST /api/login
    public function login(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->request->getJSON(true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->failValidationErrors('Email and password are required.');
        }

        $user = $this->users->where('email', $data['email'])->first();

        if (!$user || !password_verify($data['password'], $user->password)) {
            return $this->failUnauthorized('Invalid credentials.');
        }

        // Update last login
        $this->users->update($user->id, ['last_login_at' => date('Y-m-d H:i:s')]);

        session()->set([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'role'       => $user->role,
            'isLoggedIn' => true,
        ]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login successful',
            'user'    => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    // POST /api/logout
    public function logout(): \CodeIgniter\HTTP\ResponseInterface
    {
        session()->destroy();
        return $this->respond(['status' => 'success', 'message' => 'Logged out successfully.']);
    }

    // POST /api/password/forgot
    public function forgotPassword(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->request->getJSON(true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Email is required.'
            ], 400);
        }

        // ✅ Check if the user exists
        $user = $this->db->table('tbl_users')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Email not found.'
            ], 404);
        }

        // ✅ Generate reset token
        $token = bin2hex(random_bytes(32));

        // Store token in password_resets
        $this->db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // ✅ Build password reset link
        $resetLink = base_url('attendees/reset-password?token=' . $token);

        // ✅ Compose the email
        $emailService = \Config\Services::email();

        $emailService->setTo($email);
        $emailService->setFrom(
            env('email.fromEmail'),
            env('email.fromName')
        );
        $emailService->setSubject('Password Reset Request - EPRGlobal Events');
        $emailService->setMessage("
        <html>
        <body style='font-family:Poppins,Arial,sans-serif;color:#333;'>
            <p>Dear Participant,</p>
            <p>We received a request to reset your EPRGlobal Events Portal password. Click the button below to proceed:</p>
            <p style='margin:25px 0;text-align:center;'>
                <a href='{$resetLink}' 
                   style='background-color:#9D0F82;color:#fff;
                          text-decoration:none;padding:12px 25px;
                          border-radius:6px;font-weight:bold;'>
                    Reset My Password
                </a>
            </p>
            <p>This link will expire in <strong>1 hour</strong>. If you did not request this, please ignore this email.</p>
            <br>
            <p>Warm regards,<br><strong>EPRGlobal Events Team</strong></p>
        </body>
        </html>
    ");

        // ✅ Send email
        if (!$emailService->send()) {
            log_message('error', 'Password reset email failed: ' . print_r($emailService->printDebugger(['headers']), true));
            return $this->respond([
                'status'  => 'error',
                'message' => 'Unable to send password reset email. Please try again later.'
            ], 500);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Password reset link has been sent successfully to your email address.'
        ]);
    }

    public function resetPassword(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->request->getJSON(true);
        $token = $data['token'] ?? null;
        $newPassword = $data['password'] ?? null;

        if (!$token || !$newPassword) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Token and new password are required.'
            ], 400);
        }

        // ✅ Look up token
        $reset = $this->db->table('password_resets')
            ->where('token', $token)
            ->get()
            ->getRowArray();

        if (!$reset) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Invalid or expired token.'
            ], 400);
        }

        // ✅ Check expiration (1 hour)
        $created = strtotime($reset['created_at']);
        if (time() - $created > 3600) { // 1 hour = 3600 seconds
            $this->db->table('password_resets')->where('token', $token)->delete();
            return $this->respond([
                'status' => 'error',
                'message' => 'Token expired. Please request a new password reset link.'
            ], 400);
        }

        // ✅ Hash new password
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        // ✅ Update user’s password
        $this->db->table('tbl_users')
            ->where('email', $reset['email'])
            ->update(['password' => $hashed, 'updated_at' => date('Y-m-d H:i:s')]);

        // ✅ Remove the used token
        $this->db->table('password_resets')->where('token', $token)->delete();

        return $this->respond([
            'status' => 'success',
            'message' => 'Password has been successfully reset. You may now log in.'
        ]);
    }

}
