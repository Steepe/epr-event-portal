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
use App\Modules\MobileApp\Models\SpeakersModel;

class PastConference extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 🗂️ Archive of past conferences
    public function archive()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $conferences = $this->db->table('tbl_conferences')
            ->where('status', 'past')
            ->orderBy('start_date', 'DESC')
            ->get()->getResultArray();

        return module_view('MobileApp', 'past_conferences', [
            'conferences' => $conferences,
        ]);
    }

    // 🧩 List of sessions
    public function index($conference_id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        if (!$conference_id) {
            return redirect()->to(site_url('mobile/past-conferences'));
        }

        $sessionModel = new SessionModel();
        $speakerModel = new SpeakersModel();
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
            $s['speakers'] = $speakerModel->getSpeakersBySession($s['sessions_id']);
            $sessionsByDate[$s['event_date']][] = $s;
        }

        $user = $this->db->table('tbl_users')->where('id', session('user_id'))->get()->getRowArray();

        return module_view('MobileApp', 'past_conference_list', [
            'conference'     => $conference,
            'sessionsByDate' => $sessionsByDate,
            'premium'        => (int) ($user['premium_access'] ?? 0),
        ]);
    }

    // 🎥 Individual past sessions open in the shared session screen
    public function viewSession($session_id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        if (!$session_id) {
            return redirect()->to(site_url('mobile/past-conferences'));
        }

        return redirect()->to(site_url('mobile/session/' . (int) $session_id));
    }
}
