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
use App\Modules\Web\Models\ConferenceModel;
use App\Modules\Web\Models\ConferenceSessionsModel;
use App\Traits\AttendeeTrait;

class AgendaController extends BaseController
{
    use AttendeeTrait;

    public function index()
    {
        $session = session();

        // Ensure user logged in
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        // Get live conference ID from session
        $liveConferenceId = $session->get('live-conference-id');
        if (empty($liveConferenceId)) {
            return redirect()->to(base_url('attendees/home'))
                ->with('error', 'No live conference selected.');
        }

        // Pull conference + sessions using Web module models
        $conferenceModel = new ConferenceModel();
        $sessionsModel = new ConferenceSessionsModel();

        $conference = $conferenceModel->find($liveConferenceId);
        $sessions = $sessionsModel
            ->where('conference_id', $liveConferenceId)
            ->orderBy('event_date', 'ASC')
            ->findAll();

        // Attach minimal attendee context
        $data = [
            'conference' => $conference,
            'attendee_sessions' => $sessions,
            'global_attendee_details' => $this->getAttendeeData(),
        ];

        return module_view('Web', 'agenda', $data);
    }
}
