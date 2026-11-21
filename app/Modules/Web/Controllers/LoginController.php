<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 05:08
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->has('attendee_id') || (new \App\Services\AttendeeRememberMe())->restore()) {
            return redirect()->to(site_url('attendees/start'));
        }

        return module_view('Web', 'login');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();
        PresenceController::clearForUser((int) $session->get('user_id'));
        (new \App\Services\AttendeeRememberMe())->clearCurrentDevice();

        // Destroy all session data
        $session->destroy();

        return redirect()->to('attendees/login');
    }

}
