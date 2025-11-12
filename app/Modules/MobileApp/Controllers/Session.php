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
use App\Modules\MobileApp\Models\SpeakerModel;

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
        $speakerModel = new SpeakerModel();

        // 🧠 Fetch session details
        $session = $sessionModel->where('sessions_id', $sessions_id)->first();
        if (empty($session)) {
            return redirect()->to(site_url('mobile/agenda/' . session()->get('live-conference-id')))
                ->with('error', 'Session not found.');
        }

        // 👥 Fetch linked speakers (joined with tbl_speakers)
        $speakers = $speakerModel
            ->select('tbl_speakers.speaker_name, tbl_speakers.speaker_title, tbl_speakers.speaker_company, tbl_speakers.speaker_photo')
            ->join('tbl_speakers', 'tbl_speakers.speaker_id = tbl_session_speakers.speaker_id', 'left')
            ->where('tbl_session_speakers.sessions_id', $sessions_id)
            ->findAll();

        $timezone = session('reg_country') === 'Nigeria' ? 'Africa/Lagos' : 'UTC';

        return module_view('MobileApp', 'session_detail', [
            'session'   => $session,
            'speakers'  => $speakers,
            'timezone'  => $timezone,
        ]);
    }
}
