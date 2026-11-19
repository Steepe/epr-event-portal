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

    public function backgroundRecording(): string
    {
        return module_view('Web', 'background_recording');
    }

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
        $conference = $access->liveConference();
        $accessRequiresPayment = $access->requiresPayment($conference);
        $paidUser = $conference && $accessRequiresPayment
            ? $access->attendeeHasPaid(
                (int) $session->get('user_id'),
                (int) $session->get('attendee_id'),
                (int) $conference['conference_id']
            )
            : false;
        $ticket = $conference
            ? $access->ticketPriceForCountry(
                (int) $conference['conference_id'],
                (string) $session->get('reg_country')
            )
            : ['amount' => null, 'currency' => 'USD'];

        if ($conference) {
            $session->set('live-conference-id', (int) $conference['conference_id']);
        }

        return module_view('Web', 'lobby', [
            'global_attendee_details' => $attendee,
            'attendee_sessions' => $sessions,
            'unread_messages' => $unread,
            'page_title' => 'Event Lobby',
            'access_requires_payment' => $accessRequiresPayment,
            'checkout_enabled' => $access->checkoutEnabled(),
            'payment_notice' => $accessRequiresPayment && ! $paidUser,
            'ticket_price' => $ticket['amount'],
            'ticket_currency' => $ticket['currency'],
        ]);

    }

}
