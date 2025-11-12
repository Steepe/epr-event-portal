<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 12:04
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

class EmergenceBoothController extends BaseController
{
    public function index()
    {
        // No dynamic data needed for now — static booth items
        echo module_view('MobileApp', 'emergence_booth');
    }

    public function sendSupportEmail()
    {
        $request = service('request');
        $data = $request->getJSON(true);

        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required.']);
        }

        $email = \Config\Services::email();

        // 📨 Load settings from .env automatically
        $email->setFrom(env('email.fromEmail'), env('email.fromName'));
        $email->setTo('registration@eprglobal.com');
        $email->setReplyTo($data['email'], $data['name']);

        // ✉️ Subject and Body
        $subject = 'Message from ' . $data['name'] . ' <' . $data['email'] . '>';
        $body = "
            <div style='font-family: Arial, sans-serif; font-size: 15px; color: #333;'>
                <p><strong>Name:</strong> {$data['name']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <hr>
                <p style='margin-top: 10px;'><strong>Message:</strong></p>
                <p>" . nl2br(esc($data['message'])) . "</p>
            </div>
        ";

        $email->setSubject($subject);
        $email->setMessage($body);

        if ($email->send()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Your message has been sent successfully!'
            ]);
        }

        // Log and show email debug if sending fails
        log_message('error', 'Support email failed: ' . $email->printDebugger(['headers']));
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unable to send message. Please try again later.'
        ]);
    }

}
