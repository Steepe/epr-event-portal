<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 12:42
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\UserModel;
use App\Modules\MobileApp\Models\AttendeeModel;

class RegisterController extends BaseController
{
    public function index()
    {
        return module_view('MobileApp', 'register');
    }

    public function process()
    {
        $request = service('request');
        $validation = service('validation');

        $validation->setRules([
            'firstname' => 'required|min_length[2]',
            'lastname'  => 'required|min_length[2]',
            'email'     => 'required|valid_email|is_unique[tbl_users.email]',
            'password'  => 'required|min_length[6]',
            'country'   => 'required',
        ]);

        if (!$validation->withRequest($request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new UserModel();
        $attendeeModel = new AttendeeModel();

        // 🧩 Create User
        $userId = $userModel->insert([
            'email'     => $request->getPost('email'),
            'password'  => password_hash($request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => 'attendee',
            'status'    => 1,
        ]);

        // 🧍 Create Attendee profile
        $attendeeModel->insert([
            'attendee_id' => $userId,
            'firstname'   => $request->getPost('firstname'),
            'lastname'    => $request->getPost('lastname'),
            'telephone'   => $request->getPost('telephone'),
            'country'     => $request->getPost('country'),
            'state'       => $request->getPost('state'),
            'city'        => $request->getPost('city'),
            'is_verified' => 1,
        ]);

        // 🔑 Initialize session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $userId,
            'name'       => $request->getPost('firstname') . ' ' . $request->getPost('lastname'),
            'email'      => $request->getPost('email'),
        ]);

        return redirect()->to(site_url('mobile/lobby'))->with('success', 'Welcome aboard!');
    }
}
