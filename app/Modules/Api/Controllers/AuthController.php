<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:59
 */


namespace App\Modules\Api\Controllers;

use App\Modules\Api\Models\TblUsersModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected $users;

    public function __construct()
    {
        $this->users = new TblUsersModel();
    }

    // POST /api/login
    public function login()
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

        // Create CI4 session (for now; can switch to JWT later)
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
    public function logout()
    {
        session()->destroy();
        return $this->respond(['status' => 'success', 'message' => 'Logged out successfully.']);
    }
}
