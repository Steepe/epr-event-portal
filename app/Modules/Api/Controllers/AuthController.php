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
            return $this->respond(['status' => 'error', 'message' => 'Email is required'], 400);
        }

        // ✅ Correct table and column
        $user = $this->db->table('tbl_users')->where('email', $email)->get()->getRowArray();

        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Email not found'], 404);
        }

        // ✅ Generate secure reset token
        $token = bin2hex(random_bytes(32));

        // Store token in password_resets (you’ll need this table)
        $this->db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Build password reset link
        $resetLink = base_url('attendees/reset-password?token=' . $token);

        // (Optional) Send email here using your mail service

        return $this->respond([
            'status'  => 'success',
            'message' => 'Password reset link sent successfully.',
            'link'    => $resetLink, // helpful for testing
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
