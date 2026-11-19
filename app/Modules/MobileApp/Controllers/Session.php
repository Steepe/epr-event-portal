<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:58
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SessionModel;
use App\Modules\MobileApp\Models\SpeakersModel;

class Session extends BaseController
{
    public function detail($sessions_id = null)
    {
        // 🔒 Require login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        if (empty($sessions_id)) {
            return redirect()->to(site_url('mobile/home'));
        }

        $sessionModel = new SessionModel();
        $speakerModel = new SpeakersModel();

        // 🧠 Fetch session details
        $session = $sessionModel->where('sessions_id', $sessions_id)->first();
        if (empty($session)) {
            return redirect()->to(site_url('mobile/agenda/' . session()->get('live-conference-id')))
                ->with('error', 'Session not found.');
        }

        // 👥 Fetch linked speakers safely
        $speakers = $speakerModel->getSpeakersBySession($sessions_id);

        $timezone = session('reg_country') === 'Nigeria' ? 'Africa/Lagos' : 'UTC';

        $db         = db_connect();
        $user       = $db->table('tbl_users')->where('id', session('user_id'))->get()->getRowArray();
        $conference = $db->table('tbl_conferences')
            ->where('conference_id', $session['conference_id'] ?? 0)
            ->get()->getRowArray();

        return module_view('MobileApp', 'session_detail', [
            'session'    => $session,
            'speakers'   => $speakers,
            'timezone'   => $timezone,
            'conference' => $conference,
            'premium'    => (int) ($user['premium_access'] ?? 0),
        ]);
    }

    public function view($sessions_id)
    {
        $sessionModel = new SessionModel();
        $speakerModel = new SpeakersModel();

        $session = $sessionModel->where('sessions_id', $sessions_id)->first();
        if (!$session) {
            return redirect()->to(site_url('mobile/agenda'))->with('error', 'Session not found.');
        }

        $speakers = $speakerModel->getSpeakersBySession($sessions_id);

        $data = [
            'session'  => $session,
            'speakers' => $speakers,
        ];

        echo module_view('MobileApp', 'sessions/view', $data);
    }

}
