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

    // Both desktop and mobile use this one reset flow.
    public function forgotPassword(): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $data = $this->request->getJSON(true) ?? [];
            if (!is_string($data['email'] ?? null)) {
                throw new \InvalidArgumentException('Please enter a valid email address.');
            }
            if (!service('throttler')->check('attendee-reset-' . hash('sha256', $this->request->getIPAddress()), 5, 900)) {
                return $this->respond(['status' => 'error', 'message' => 'Too many requests. Please try again in 15 minutes.'], 429);
            }
            (new \App\Libraries\AttendeePasswordReset($this->db))->sendLink($data['email']);
            return $this->respond(['status' => 'success', 'message' => \App\Libraries\AttendeePasswordReset::SENT_MESSAGE]);
        } catch (\InvalidArgumentException $error) {
            return $this->respond(['status' => 'error', 'message' => $error->getMessage()], 400);
        } catch (\Throwable $error) {
            // Never log the token, submitted passwords, or SMTP credentials.
            log_message('error', 'Attendee password reset email failed ({type}). Check reset-table migration and mail configuration.', ['type' => get_class($error)]);
            return $this->respond(['status' => 'error', 'message' => 'Unable to send the reset email. Please try again later or contact support.'], 503);
        }
    }

    public function resetPassword(): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $data = $this->request->getJSON(true) ?? [];
            foreach (['token', 'password', 'confirm_password'] as $field) {
                if (!is_string($data[$field] ?? null)) {
                    throw new \InvalidArgumentException('The reset link, password and confirmation are required.');
                }
            }
            (new \App\Libraries\AttendeePasswordReset($this->db))->reset($data['token'], $data['password'], $data['confirm_password']);
            return $this->respond(['status' => 'success', 'message' => 'Password has been successfully reset. You may now log in.']);
        } catch (\InvalidArgumentException $error) {
            return $this->respond(['status' => 'error', 'message' => $error->getMessage()], 400);
        } catch (\Throwable $error) {
            log_message('error', 'Attendee password reset failed ({type}).', ['type' => get_class($error)]);
            return $this->respond(['status' => 'error', 'message' => 'Unable to save your password. Please try again later.'], 503);
        }
    }
}
