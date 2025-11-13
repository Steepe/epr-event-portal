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
use App\Modules\Api\Models\TblConferencesModel;
use App\Modules\Api\Models\TblTicketPricesModel;
use App\Modules\Api\Models\TblAttendeePaymentsModel;

class HomeController extends BaseController
{
    public function index()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        // ✅ Get the live conference securely through helper
        $conferenceData = api_get('conferences/live');
        $conference     = $conferenceData['data'] ?? null;

        $isPaid = false;
        $ticketPrice = null;
        $ticketCurrency = 'USD';

        if ($conference) {
            // ✅ Determine ticket price via API
            //$slug = $conference['slug'];
            //$priceData = api_get("ticket-prices?conference_slug={$slug}");
            $ticket = $priceData['data'][0] ?? null;

            // Simplify logic for country → currency mapping
            $country = strtolower($session->get('reg_country') ?? '');
            if ($ticket) {
                switch ($country) {
                    case 'nigeria':
                        $ticketPrice = $ticket['amount_naira'];
                        $ticketCurrency = 'NGN';
                        break;
                    case 'kenya':
                        $ticketPrice = $ticket['amount_shillings'];
                        $ticketCurrency = 'KES';
                        break;
                    case 'south africa':
                        $ticketPrice = $ticket['amount_rands'];
                        $ticketCurrency = 'ZAR';
                        break;
                    default:
                        $ticketPrice = $ticket['amount_dollar'];
                        $ticketCurrency = 'USD';
                        break;
                }
            }

            // ✅ Check if attendee has paid (you can extend api_get for POST later)
            $payments = api_get("attendee-payments/{$session->get('attendee_id')}");
            $isPaid = !empty($payments['data']);
        }

        $data = [
            'conference' => $conference,
            'ticket_price' => $ticketPrice,
            'ticket_currency' => $ticketCurrency,
            'is_paid' => $isPaid,
        ];

        return module_view('Web', 'home', $data);
    }
}
