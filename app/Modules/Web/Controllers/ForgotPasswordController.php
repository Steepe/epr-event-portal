<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class ForgotPasswordController extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('attendees/login') . '?reset=1');
    }

    public function resetForm($token = null)
    {
        $this->response->setHeader('Cache-Control', 'no-store')->setHeader('Referrer-Policy', 'no-referrer');
        $token = $token ?? $this->request->getGet('token');
        $token = is_string($token) ? $token : '';
        // The reset endpoint validates expiry and single use before saving.
        return module_view('Web', 'reset_password', ['token' => $token]);
    }
}
