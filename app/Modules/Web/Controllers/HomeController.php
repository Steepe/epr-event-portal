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
    private const DEFAULT_WELCOME_VIDEO_URL = 'https://iframe.mediadelivery.net/embed/405384/60589a7b-3fe7-40fb-a2ae-3c024f23e01e?loop=false&muted=false&preload=true&responsive=true';

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
            'welcome_video_url' => $conference['welcome_video_url'] ?? self::DEFAULT_WELCOME_VIDEO_URL,
            'ticket_price' => $ticketPrice,
            'ticket_currency' => $ticketCurrency,
            'is_paid' => $isPaid,
            'portal_locked' => $portalLocked,
            'checkout_enabled' => $access->checkoutEnabled(),
        ];

        return module_view('Web', 'home', $data);
    }
}
