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
use App\Services\PortalAccessService;
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

        $attendee = $this->getAttendeeData();
        $sessions = $this->getAttendeeSessions();
        $unread   = $this->getUnreadMessagesCount();
        $access = new PortalAccessService();

        return module_view('Web', 'lobby', [
            'global_attendee_details' => $attendee,
            'attendee_sessions' => $sessions,
            'unread_messages' => $unread,
            'page_title' => 'Event Lobby',
            'access_requires_payment' => $access->requiresPayment(),
            'checkout_enabled' => $access->checkoutEnabled(),
        ]);

    }

}
