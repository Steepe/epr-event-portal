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
    private const DEFAULT_WELCOME_VIDEO_URL = 'https://iframe.mediadelivery.net/embed/405384/60589a7b-3fe7-40fb-a2ae-3c024f23e01e?loop=false&muted=false&preload=true&responsive=true';

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
            'welcome_video_url' => !empty($conference['welcome_video_url'])
                ? $conference['welcome_video_url']
                : self::DEFAULT_WELCOME_VIDEO_URL,
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
