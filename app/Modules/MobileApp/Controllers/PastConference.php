<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 07:21
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SessionModel;

class PastConference extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 🧩 List of sessions
    public function index($conference_id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        if (!$conference_id) {
            return redirect()->to(site_url('mobile/home'));
        }

        $sessionModel = new SessionModel();

        $sessions = $sessionModel
            ->where('conference_id', $conference_id)
            ->orderBy('event_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->findAll();

        $conference = $this->db->table('tbl_conferences')
            ->where('conference_id', $conference_id)
            ->get()->getRowArray();

        $sessionsByDate = [];
        foreach ($sessions as $s) {
            $sessionsByDate[$s['event_date']][] = $s;
        }

        return module_view('MobileApp', 'past_conference_list', [
            'conference'     => $conference,
            'sessionsByDate' => $sessionsByDate,
        ]);
    }

    // 🎥 View individual past session
    public function viewSession($session_id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $sessionModel = new SessionModel();

        $session = $sessionModel->find($session_id);

        if (!$session) {
            return redirect()->to(site_url('mobile/home'));
        }

        // fetch speakers
        $speakers = $this->db->table('tbl_session_speakers ss')
            ->select('sp.speaker_name, sp.speaker_title, sp.speaker_company, sp.speaker_photo')
            ->join('tbl_speakers sp', 'sp.speaker_id = ss.speaker_id')
            ->where('ss.sessions_id', $session_id)
            ->get()->getResultArray();

        return module_view('MobileApp', 'past_conference_session_detail', [
            'session'  => $session,
            'speakers' => $speakers,
        ]);
    }
}
