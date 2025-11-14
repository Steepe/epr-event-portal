<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 21:22
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to(site_url('mobile/home'));
        }

        return module_view('MobileApp', 'login');
    }

    public function attemptLogin()
    {
        $email = trim($this->request->getPost('email'));
        $password = trim($this->request->getPost('password'));

        if (empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Email and password are required.')->withInput();
        }

        $userModel = new \App\Modules\MobileApp\Models\UserModel();
        $attendeeModel = new \App\Modules\MobileApp\Models\AttendeeModel();

        // 🔹 Find user by email
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No account found for that email.')->withInput();
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid password.')->withInput();
        }

        // 🔹 Fetch attendee info (optional but strongly recommended)
        $attendee = $attendeeModel
            ->select('firstname, lastname, profile_picture')
            ->where('attendee_id', $user['id'])
            ->first();

        // 🔹 Prepare session data
        $sessionData = [
            'user_id'     => $user['id'],
            'user_email'  => $user['email'],
            'user_role'   => $user['role'],
            'uuid'        => $user['uuid'] ?? null,
            'isLoggedIn'  => true,
            'attendee_id'=>  $user['id'],
            'firstname'   => $attendee['firstname'] ?? '',
            'lastname'    => $attendee['lastname'] ?? '',
            'fullname'    => isset($attendee)
                ? trim($attendee['firstname'] . ' ' . $attendee['lastname'])
                : '',
            'profile_picture' => $attendee['profile_picture'] ?? null,
        ];

        session()->set($sessionData);

        // 🔹 Update last login
        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        // 🔹 Redirect
        return redirect()->to(site_url('mobile/home'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('mobile/login'));
    }

    public function forgotPassword()
    {
        // This loads your forgot password view
        return module_view('MobileApp', 'forgot_password');
    }

    public function resetPassword($token = null): string
    {
        // This loads your reset password form
        return module_view('MobileApp', 'reset_password', ['token' => $token]);
    }
}
