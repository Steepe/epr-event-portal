<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 14:35
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblConferencesModel;
use App\Traits\AttendeeTrait;

class LobbyController extends BaseController
{
    use AttendeeTrait;

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {

        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $conferenceModel = new TblConferencesModel();
        $conference = $conferenceModel->where('is_live', 1)->first();

        $attendee = $this->getAttendeeData();
        $sessions = $this->getAttendeeSessions();
        $unread   = $this->getUnreadMessagesCount();

        return module_view('Web', 'lobby', [
            'conference' => $conference,
            'global_attendee_details' => $attendee,
            'attendee_sessions' => $sessions,
            'unread_messages' => $unread,
            'page_title' => 'Event Lobby',
        ]);

    }

}
