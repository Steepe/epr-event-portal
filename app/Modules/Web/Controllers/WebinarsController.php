<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 20:29
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class WebinarsController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        // 🧭 Get all webinars (newest first)
        $builder = $this->db->table('tbl_webinars');
        $builder->orderBy('event_date', 'DESC');
        $webinars = $builder->get()->getResultArray();

        $data = [
            'webinars'  => $webinars,
            'page_title' => 'EPR Webinars',
            'timezone'   => $session->get('user_timezone') ?? 'Africa/Lagos',
            'plan'       => $session->get('plan') ?? 1,
        ];

        return module_view('Web', 'webinars_list', $data);
    }
}
