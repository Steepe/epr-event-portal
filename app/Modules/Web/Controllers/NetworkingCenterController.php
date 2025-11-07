<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 30/10/2025
 * Time: 15:35
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class NetworkingCenterController extends BaseController
{
    public function index()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $data = [
            'attendee_id' => $session->get('attendee_id'),
            'country'     => $session->get('reg_country') ?? 'Nigeria',
            'plan'        => $session->get('plan') ?? 1,
            'page_title'  => 'Networking Center',
        ];

        return module_view('Web', 'networking_center', $data);
    }
}
