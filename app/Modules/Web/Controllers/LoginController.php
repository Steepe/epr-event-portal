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

    public function logout(): \CodeIgniter\HTTP\ResponseInterface
    {
        $session = session();

        // Destroy all session data
        $session->destroy();

        return $this->respond([
            'status'  => 'success',
            'message' => 'Logout successful. Session destroyed.'
        ]);
    }

    public function resetPassword(): string
    {
        return module_view('Web', 'reset_password');
    }


}
