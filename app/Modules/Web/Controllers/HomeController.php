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

        // The guided avatar experience is intentionally paused during the live
        // conference. Send attendees straight to the lightweight Lobby instead.
        return redirect()->to(site_url('attendees/lobby'));
    }
}
