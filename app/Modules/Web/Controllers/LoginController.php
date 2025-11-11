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
        return module_view('Web', 'login');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        // Destroy all session data
        $session->destroy();

        return redirect()->to('attendees/login');
    }

    public function resetPassword(): string
    {
        return module_view('Web', 'reset_password');
    }


}
