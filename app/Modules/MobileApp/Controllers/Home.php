<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 16:09
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\ConferenceModel;

class Home extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $conferenceModel = new ConferenceModel();

        $conference = $conferenceModel
            ->where('status', 'live')
            ->orderBy('year', 'DESC')
            ->first();

        if (!empty($conference['conference_id'])) {
            session()->set('live-conference-id', $conference['conference_id']);
        }

        return module_view('MobileApp', 'home', [
            'conference' => $conference,
        ]);
    }

    public function envision(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        return module_view('MobileApp', 'envision');

    }
}
