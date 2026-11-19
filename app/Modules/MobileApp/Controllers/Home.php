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

        // A welcome film is conference content, not a global default. Keeping an
        // older event's player here made a new conference appear to have the
        // wrong video when its own film had not been configured yet.
        $welcomeVideoUrl = trim((string) ($conference['welcome_video_url'] ?? ''));

        return module_view('MobileApp', 'home', [
            'conference' => $conference,
            'welcome_video_url' => filter_var($welcomeVideoUrl, FILTER_VALIDATE_URL)
                ? $welcomeVideoUrl
                : '',
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
