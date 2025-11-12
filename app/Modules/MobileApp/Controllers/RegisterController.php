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
use App\Modules\MobileApp\Models\CountryModel;
use App\Modules\MobileApp\Models\UserModel;
use App\Modules\MobileApp\Models\AttendeeModel;
use Config\Services;

class RegisterController extends BaseController
{
    public function index()
    {
        $countryModel = new CountryModel();
        $data['countries'] = $countryModel->orderBy('country_name', 'ASC')->findAll();

        return module_view('MobileApp', 'register', $data);
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

        // Create user
        $userId = $userModel->insert([
            'email'     => $request->getPost('email'),
            'password'  => password_hash($request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => 'attendee',
            'status'    => 1,
        ]);

        // Create attendee record
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

        // ✅ Send branded HTML welcome email
        $emailService = Services::email();

        $to = $request->getPost('email');
        $name = $request->getPost('firstname') . ' ' . $request->getPost('lastname');

        $subject = "🎉 Welcome to EPR Global’s Event Portal! Your Access Is Confirmed";

        $message = '
        <html>
        <body style="margin:0;padding:0;background-color:#f4f4f4;font-family:\'Poppins\',sans-serif;">
            <div style="max-width:600px;margin:30px auto;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,0.1);">
                <div style="background:linear-gradient(90deg,#9D0F82,#EFB11E);padding:25px;text-align:center;">
                    <img src="https://portal.eprglobal.com/assets/images/eventslogo.png" alt="EPR Global" style="width:180px;">
                </div>

                <div style="padding:30px;text-align:left;color:#333333;">
                    <h2 style="color:#9D0F82;margin-bottom:15px;">Hello ' . ucfirst($request->getPost('firstname')) . ',</h2>

                    <p>Welcome to <strong>EPR Global’s Event Portal</strong>, your digital gateway to inspiration, empowerment, and transformation.</p>

                    <p>You now have full access to our library of past Emergence Conferences, webinars, and powerful sessions from the Women’s Professional Network (WPN). Each resource is designed to help you grow spiritually, personally, and professionally.</p>

                    <p>Take time to revisit your favourite moments or catch up on sessions you may have missed. Every talk is an opportunity to gain clarity, grow in confidence, and keep your genius in motion.</p>

                    <p>If you have any questions or need support, reach out anytime at 
                    <a href="mailto:support@eprglobal.com" style="color:#9D0F82;font-weight:bold;">support@eprglobal.com</a>.</p>

                    <p>We’re thrilled to have you with us! 💎</p>

                    <div style="text-align:center;margin-top:35px;">
                        <a href="https://portal.eprglobal.com" 
                           style="background:#9D0F82;color:#fff;text-decoration:none;padding:12px 30px;
                                  border-radius:30px;font-weight:600;display:inline-block;transition:0.3s;">
                            Access the Portal
                        </a>
                    </div>

                    <p style="margin-top:40px;color:#777;font-size:14px;text-align:center;">
                        With excitement,<br><strong>The EPR Global Team</strong><br>
                        <em>Building the Women who will Build Institutions</em>
                    </p>
                </div>

                <div style="background:#150020;color:#fff;text-align:center;padding:15px 10px;font-size:12px;">
                    &copy; ' . date('Y') . ' EPR Global. All Rights Reserved.
                </div>
            </div>
        </body>
        </html>';

        $emailService->setFrom(env('email.fromEmail'), env('email.fromName'));
        $emailService->setTo($to);
        $emailService->setSubject($subject);
        $emailService->setMessage($message);
        $emailService->setMailType('html');
        $emailService->setReplyTo($to, $name);
        //$emailService->send();



        // Create login session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $userId,
            'name'       => $name,
            'email'      => $to,
        ]);

        return redirect()->to(site_url('mobile/lobby'))->with('success', 'Registration successful! Welcome to EPR Global.');
    }
}
