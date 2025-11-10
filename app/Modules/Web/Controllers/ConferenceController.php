<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 14:59
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ConferenceController extends BaseController
{
    public function view($conferenceId)
    {
        $session = session();

        // 🔐 Ensure attendee is logged in
        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        try {
            $db = \Config\Database::connect();

            // 🧩 Fetch the conference details
            $conf = $db->table('tbl_conferences')
                ->where('conference_id', $conferenceId)
                ->get()
                ->getRowArray();

            if (!$conf) {
                return redirect()->to(base_url('attendees/lobby'))
                    ->with('error', 'Conference not found.');
            }

            // 🧩 Fetch sessions for this conference
            $sessions = $db->table('tbl_conference_sessions AS s')
                ->select('s.*, GROUP_CONCAT(sp.speaker_name SEPARATOR ", ") AS speakers')
                ->join('tbl_session_speakers AS ss', 'ss.sessions_id = s.sessions_id', 'left')
                ->join('tbl_speakers AS sp', 'sp.speaker_id = ss.speaker_id', 'left')
                ->where('s.conference_id', $conferenceId)
                ->groupBy('s.sessions_id')
                ->orderBy('s.event_date', 'ASC')
                ->orderBy('s.start_time', 'ASC')
                ->get()
                ->getResultArray();

            // 🧠 Store this conference ID in session if needed
            $session->set('current-conference-id', $conferenceId);

            // 📦 Prepare data for the view
            $data = [
                'conference' => $conf,
                'sessions'   => $sessions,
                'attendee'   => $session->get('attendee_id'),
                'page_title' => $conf['title'] ?? 'Conference',
            ];

            return module_view('Web', 'conference_detail', $data);

        } catch (DatabaseException $e) {
            log_message('error', '[ConferenceController] DB Error: ' . $e->getMessage());
            return redirect()->to(base_url('attendees/lobby'))
                ->with('error', 'Error loading conference details.');
        }
    }
}
