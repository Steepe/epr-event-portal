<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 06:10
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DataException;

class AgendaController extends BaseController
{
    public function index($conferenceId = null)
    {
        $session = session();

        // 🧱 Ensure user is logged in
        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        // 🧠 Use stored live conference ID if none passed
        $conferenceId = $conferenceId ?? $session->get('live-conference-id');

        if (! $conferenceId) {
            return redirect()->to(base_url('attendees/home'))
                ->with('error', 'No active conference found.');
        }

        $db = db_connect();

        try {
            // Fetch sessions with speakers in one query
            $builder = $db->table('tbl_conference_sessions s')
                ->select('s.*, sp.speaker_name, sp.speaker_title, sp.speaker_company')
                ->join('tbl_session_speakers ss', 'ss.sessions_id = s.sessions_id', 'left')
                ->join('tbl_speakers sp', 'sp.speaker_id = ss.speaker_id', 'left')
                ->where('s.conference_id', $conferenceId)
                ->orderBy('s.event_date', 'ASC')
                ->orderBy('s.start_time', 'ASC');

            $results = $builder->get()->getResultArray();

            // Group sessions by date
            $grouped = [];
            foreach ($results as $row) {
                $eventDate = $row['event_date'];

                // Group speakers under each session
                $sessionId = $row['sessions_id'];
                if (!isset($grouped[$eventDate][$sessionId])) {
                    $grouped[$eventDate][$sessionId] = [
                        'sessions_id'   => $row['sessions_id'],
                        'sessions_name' => $row['sessions_name'],
                        'event_date'    => $row['event_date'],
                        'start_time'    => $row['start_time'],
                        'end_time'      => $row['end_time'],
                        'access_level'  => $row['access_level'],
                        'description'   => $row['description'],
                        'vimeo_id'      => $row['vimeo_id'],
                        'workbook'      => $row['workbook'],
                        'tags'          => $row['tags'],
                        'speakers'      => [],
                    ];
                }

                // Append speaker if available
                if (!empty($row['speaker_name'])) {
                    $grouped[$eventDate][$sessionId]['speakers'][] = [
                        'name'     => $row['speaker_name'],
                        'title'    => $row['speaker_title'],
                        'company'  => $row['speaker_company'],
                    ];
                }
            }

            // Flatten sessions per date for rendering
            foreach ($grouped as $date => $sessions) {
                $grouped[$date] = array_values($sessions);
            }

            $data = [
                'sessionsByDate' => $grouped,
                'timezone'       => $session->get('user_timezone') ?? 'Africa/Lagos',
                'plan'           => $session->get('plan') ?? 1,
                'attendee_id'    => $session->get('attendee_id'),
            ];

            return module_view('Web', 'agenda', $data);

        } catch (DataException $e) {
            log_message('error', 'Agenda load failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load sessions.');
        }
    }

    public function view_past_conference($conferenceId): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        // 🧱 Ensure user is logged in
        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }


        if (! $conferenceId) {
            return redirect()->to(base_url('attendees/home'))
                ->with('error', 'No active conference found.');
        }

        $db = db_connect();

        try {
            // Fetch all sessions for that conference
            $sessions = $db->table('tbl_conference_sessions')
                ->where('conference_id', $conferenceId)
                ->orderBy('event_date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();

            // Group sessions by event_date
            $grouped = [];
            foreach ($sessions as $s) {
                $grouped[$s['event_date']][] = $s;
            }

            $data = [
                'sessionsByDate' => $grouped,
                'timezone'       => $session->get('user_timezone') ?? 'Africa/Lagos',
                'plan'           => $session->get('plan') ?? 1,
                'attendee_id'    => $session->get('attendee_id'),
                'conf_title'     => $s['sessions_name'],
            ];

            return module_view('Web', 'past_agenda', $data);

        } catch (DataException $e) {
            log_message('error', 'Agenda load failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load sessions.');
        }
    }
}
