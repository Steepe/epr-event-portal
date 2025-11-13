<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:40
 */


namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SessionModel;
use App\Modules\MobileApp\Models\SpeakersModel;

class Agenda extends BaseController
{
    public function index($conference_id = null): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        if (empty($conference_id)) {
            return redirect()->to(site_url('mobile/home'));
        }

        $sessionModel  = new SessionModel();
        $speakerModel  = new SpeakersModel();

        // Fetch all sessions
        $sessions = $sessionModel
            ->where('conference_id', $conference_id)
            ->orderBy('event_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->findAll();

        // Attach speakers
        foreach ($sessions as &$session) {
            $session['speakers'] = $speakerModel->getSpeakersBySession($session['sessions_id']);
        }

        return module_view('MobileApp', 'agenda', [
            'sessions'       => $sessions,
            'conference_id'  => $conference_id,
        ]);
    }
}
