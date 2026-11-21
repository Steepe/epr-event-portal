<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 06:29
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
class HomeController extends BaseController
{
    public function index()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        return (new PlatformTourController())->home();
    }
}
