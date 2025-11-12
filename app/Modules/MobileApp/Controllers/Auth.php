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

        $model = new UserModel();
        $user = $model->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No account found for that email.')->withInput();
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid password.')->withInput();
        }

        // Store login session
        session()->set([
            'user_id'     => $user['id'],
            'user_email'  => $user['email'],
            'user_role'   => $user['role'],
            'uuid'        => $user['uuid'],
            'isLoggedIn'  => true,
        ]);

        // Update last login timestamp
        $model->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(site_url('mobile/home'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('mobile/login'));
    }
}
