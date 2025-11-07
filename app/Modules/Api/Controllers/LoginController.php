<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 18:10
 */


namespace App\Modules\Api\Controllers;

use App\Modules\Api\Models\TblUsersModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class LoginController extends ResourceController
{
    use ResponseTrait;

    // ✅ Add this to avoid “must not be accessed before initialization” error
    protected string $modelName = \App\Modules\Api\Models\TblUsersModel::class;
    protected string $format = 'json';

    protected TblUsersModel $users;

    public function __construct()
    {
        // You can rely on $this->model from ResourceController if you want,
        // but it’s fine to keep a custom instance for clarity:
        $this->users = new TblUsersModel();
    }

    public function login(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->request->getJSON(true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->failValidationErrors('Email and password are required.');
        }

        // ✅ Verify API Key
        $key = $this->request->getHeaderLine('X-API-KEY');
        if ($key !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API key.');
        }

        // ✅ Find user
        $user = $this->users->where('email', $data['email'])->first();

        if (!$user) {
            return $this->failNotFound('User not found.');
        }

        // ✅ Verify password (object or array)
        $passwordHash = is_object($user) ? $user->password : $user['password'];
        if (!password_verify($data['password'], $passwordHash)) {
            return $this->fail('Invalid credentials.', 401);
        }

        // ✅ Start session and store user data
        $session = session();

        // Use array syntax safely, handles both entity and array
        $userId   = is_object($user) ? $user->id : $user['id'];
        $uuid     = is_object($user) ? $user->uuid : $user['uuid'];
        $email    = is_object($user) ? $user->email : $user['email'];
        $role     = is_object($user) ? $user->role : $user['role'];
        $country  = is_object($user) ? ($user->country ?? '') : ($user['country'] ?? '');
        $fname    = is_object($user) ? ($user->firstname ?? '') : ($user['firstname'] ?? '');
        $lname    = is_object($user) ? ($user->lastname ?? '') : ($user['lastname'] ?? '');

        $session->set([
            'user_id'     => $userId,
            'uuid'        => $uuid,
            'useremail'   => $email,
            'firstname'   => $fname,
            'lastname'    => $lname,
            'role'        => $role,
            'logged_in'   => true,
            'attendee_id' => $userId,
            'reg_country' => $country,
        ]);

        // ✅ Update last login timestamp
        $this->users->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        // ✅ Return success
        return $this->respond([
            'status'  => 'success',
            'message' => 'Login successful',
            'user'    => [
                'id'    => $userId,
                'uuid'  => $uuid,
                'email' => $email,
                'role'  => $role,
            ],
        ]);
    }
}
