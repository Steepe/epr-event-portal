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
            ];

            return module_view('Web', 'past_agenda', $data);

        } catch (DataException $e) {
            log_message('error', 'Agenda load failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load sessions.');
        }
    }
}
