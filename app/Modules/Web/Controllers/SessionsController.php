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

    public function rating(int $sessionId)
    {
        $attendeeId = $this->attendeeId();
        if ($attendeeId <= 0) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Please sign in to rate this session.']);
        }

        $db = db_connect();
        if (! $db->tableExists('tbl_session_ratings')) {
            return $this->response->setStatusCode(503)->setJSON(['message' => 'Session ratings are being prepared.']);
        }

        $row = $db->table('tbl_session_ratings')
            ->select('rating')
            ->where('sessions_id', $sessionId)
            ->where('attendee_id', $attendeeId)
            ->get()
            ->getRowArray();

        return $this->response->setJSON([
            'rating' => isset($row['rating']) ? (int) $row['rating'] : null,
        ]);
    }

    public function saveRating(int $sessionId)
    {
        $attendeeId = $this->attendeeId();
        if ($attendeeId <= 0) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Please sign in to rate this session.']);
        }

        $payload = $this->request->getJSON(true);
        $ratingValue = is_array($payload) ? ($payload['rating'] ?? null) : $this->request->getPost('rating');
        $rating = filter_var($ratingValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        if ($rating === false) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Choose between one and five stars.']);
        }

        $db = db_connect();
        if (! $db->tableExists('tbl_session_ratings')) {
            return $this->response->setStatusCode(503)->setJSON(['message' => 'Session ratings are being prepared.']);
        }
        if (! $db->table('tbl_conference_sessions')->where('sessions_id', $sessionId)->countAllResults()) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Session not found.']);
        }

        $table = $db->table('tbl_session_ratings');
        $existing = $table->select('rating_id')
            ->where('sessions_id', $sessionId)
            ->where('attendee_id', $attendeeId)
            ->get()
            ->getRowArray();
        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $table->where('rating_id', (int) $existing['rating_id'])->update([
                'rating' => $rating,
                'updated_at' => $now,
            ]);
        } else {
            $table->insert([
                'sessions_id' => $sessionId,
                'attendee_id' => $attendeeId,
                'rating' => $rating,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'rating' => $rating]);
    }

    private function attendeeId(): int
    {
        return (int) (session('user_id') ?: session('attendee_id'));
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
