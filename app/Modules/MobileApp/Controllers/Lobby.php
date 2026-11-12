<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:33
 */


namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Services\PortalAccessService;

class Lobby extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $access     = new PortalAccessService();
        $conference = $access->liveConference();
        $notice     = false;
        $ticket     = ['amount' => null, 'currency' => 'USD'];

        if ($conference) {
            session()->set('live-conference-id', (int) $conference['conference_id']);

            if ($access->requiresPayment($conference)) {
                $notice = ! $access->attendeeHasPaid(
                    (int) session('user_id'),
                    (int) session('attendee_id'),
                    (int) $conference['conference_id']
                );
            }

            $ticket = $access->ticketPriceForCountry(
                (int) $conference['conference_id'],
                (string) session('reg_country')
            );
        }

        return module_view('MobileApp', 'lobby', [
            'conferenceId'    => session('live-conference-id'),
            'conference'      => $conference,
            'payment_notice'  => $notice,
            'ticket_price'    => $ticket['amount'],
            'ticket_currency' => $ticket['currency'],
        ]);
    }
}
