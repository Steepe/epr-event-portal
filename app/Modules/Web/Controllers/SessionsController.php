<?php
/**
 * SessionsController
 * Handles attendee session detail view
 *
 * Author: Oluwamayowa Steepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DataException;

class SessionsController extends BaseController
{
    public function view($sessionId = null)
    {
        $session = session();

        // 🧱 Require login
        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        if (! $sessionId) {
            return redirect()->to(base_url('attendees/agenda'))->with('error', 'Invalid session ID.');
        }

        $db = db_connect();

        try {
            // 🔹 Fetch session details
            $sessionData = $db->table('tbl_conference_sessions')
                ->where('sessions_id', $sessionId)
                ->get()
                ->getRowArray();

            if (! $sessionData) {
                return redirect()->to(base_url('attendees/agenda'))->with('error', 'Session not found.');
            }

            // 🔹 Fetch related speakers
            $speakers = $db->table('tbl_session_speakers as ss')
                ->select('sp.*')
                ->join('tbl_speakers as sp', 'sp.speaker_id = ss.speaker_id', 'left')
                ->where('ss.sessions_id', $sessionId)
                ->get()
                ->getResultArray();

            // 🔹 Fetch sponsors (if any)
            $sponsors = $db->table('tbl_session_sponsors as sr')
                ->select('sr.sponsor_name, sr.sponsor_logo, sr.sponsor_link')
                ->where('sr.sessions_id', $sessionId)
                ->get()
                ->getResultArray();

            // ✅ Prepare view data
            $data = [
                'session'    => $sessionData,
                'speakers'   => $speakers,
                'sponsors'   => $sponsors,
                'online_attendee_count' => $this->onlineAttendeeCount($db),
                'timezone'   => $session->get('user_timezone') ?? 'Africa/Lagos',
                'plan'       => $session->get('plan') ?? 1,
                'page_title' => $sessionData['sessions_name'] ?? 'Session Details'
            ];

            return module_view('Web', 'session_detail', $data);

        } catch (DataException $e) {
            log_message('critical', 'Error loading session ' . $sessionId . ': ' . $e->getMessage());
            return redirect()->to(base_url('attendees/agenda'))
                ->with('error', 'Unable to load this session.');
        }
    }

    public function onlineCount(int $sessionId)
    {
        if (! session('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Please log in.']);
        }

        return $this->response->setJSON([
            'count' => $this->onlineAttendeeCount(db_connect()),
        ]);
    }

    private function onlineAttendeeCount($db): int
    {
        if ($db->tableExists('tbl_portal_user_presence')) {
            return (int) $db->table('tbl_portal_user_presence')
                ->where('last_seen_at >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)', null, false)
                ->countAllResults();
        }

        if ($db->tableExists('tbl_user_status')) {
            return (int) $db->table('tbl_user_status')
                ->where('is_online', 1)
                ->where('last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)', null, false)
                ->countAllResults();
        }

        return 0;
    }
}
