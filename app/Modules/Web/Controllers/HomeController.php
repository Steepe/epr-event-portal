<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 06:29
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Services\PortalAccessService;

class HomeController extends BaseController
{
    public function index()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $access = new PortalAccessService();
        $conference = $access->liveConference();
        $isPaid = false;
        $ticketPrice = null;
        $ticketCurrency = 'USD';
        $portalLocked = false;

        if ($conference) {
            $session->set('live-conference-id', $conference['conference_id']);

            $price = $access->ticketPriceForCountry(
                (int) $conference['conference_id'],
                (string) $session->get('reg_country')
            );
            $ticketPrice = $price['amount'];
            $ticketCurrency = $price['currency'];
            $isPaid = $access->attendeeHasPaid(
                (int) $session->get('user_id'),
                (int) $session->get('attendee_id'),
                (int) $conference['conference_id']
            );
        }

        $data = [
            'conference' => $conference,
            'ticket_price' => $ticketPrice,
            'ticket_currency' => $ticketCurrency,
            'is_paid' => $isPaid,
            'portal_locked' => $portalLocked,
            'checkout_enabled' => $access->checkoutEnabled(),
        ];

        return module_view('Web', 'home', $data);
    }
}
