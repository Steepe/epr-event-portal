<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 20:28
 */

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class UpgradeController extends BaseController
{
    /**
     * Display the upgrade page.
     * Route: GET /attendees/upgrade/{conferenceId}
     */
    public function index($conferenceId = null): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        if (! $conferenceId) {
            return redirect()->to(base_url('attendees/home'))
                ->with('error', 'Conference ID is required.');
        }

        $db = db_connect();
        $userId = $session->get('user_id');

        // ---------------------------------------------------------
        // 1. LOAD COUNTRY LIST FROM tbl_countries FIRST
        //    (we need this BEFORE resolving attendee country)
        // ---------------------------------------------------------
        $countryRows = $db->table('tbl_countries')
            ->orderBy('country_name', 'ASC')
            ->get()->getResultArray();

        $countryList = [];        // code => name
        $countryToCurrency = [];  // code => currency
        foreach ($countryRows as $r) {
            $code = strtoupper($r['country_code']);
            $countryList[$code] = $r['country_name'];
            $countryToCurrency[$code] = strtoupper($r['currency_code'] ?? 'USD');
        }

        // ---------------------------------------------------------
        // 2. DETERMINE USER DEFAULT COUNTRY
        //    from tbl_attendees → tbl_user_profiles
        // ---------------------------------------------------------
        $selectedCountry = null;

        if ($userId) {
            // Prefer attendee record
            $attendee = $db->table('tbl_attendees')
                ->select('country')
                ->where('attendee_id', $userId)
                ->get()->getRow();

            if ($attendee && !empty($attendee->country)) {

                $attCountry = strtoupper(trim($attendee->country));

                // Case A: stored AS CODE (NG)
                if (isset($countryList[$attCountry])) {
                    $selectedCountry = $attCountry;
                }
                else {
                    // Case B: stored AS FULL NAME (Nigeria)
                    foreach ($countryList as $code => $name) {
                        if (strtoupper($name) === $attCountry) {
                            $selectedCountry = $code;
                            break;
                        }
                    }
                }
            }

            // If still not resolved, try tbl_user_profiles
            if (!$selectedCountry) {
                $profile = $db->table('tbl_user_profiles')
                    ->select('country')
                    ->where('user_id', $userId)
                    ->get()->getRow();

                if ($profile && $profile->country) {
                    $attCountry = strtoupper(trim($profile->country));

                    if (isset($countryList[$attCountry])) {
                        $selectedCountry = $attCountry;
                    } else {
                        foreach ($countryList as $code => $name) {
                            if (strtoupper($name) === $attCountry) {
                                $selectedCountry = $code;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // If still null, default to first country
        if (!$selectedCountry && !empty($countryList)) {
            $selectedCountry = array_key_first($countryList);
        }

        // ---------------------------------------------------------
        // 3. LOAD PREMIUM PRICES
        // ---------------------------------------------------------
        $priceRows = $db->table('tbl_premium_prices')
            ->where('active', 1)
            ->get()->getResultArray();

        $currencyPrices = [];   // "NGN" => 2000
        $countryPrices = [];    // "NG" => 2000

        foreach ($priceRows as $p) {
            $curr = strtoupper($p['currency']);
            $amt  = (float)$p['amount'];

            if (!empty($p['country'])) {
                $countryPrices[strtoupper($p['country'])] = $amt;
            } else {
                $currencyPrices[$curr] = $amt;
            }
        }

        $baseUSDPrice = $currencyPrices['USD'] ?? 20;

        // ---------------------------------------------------------
        // 4. PASS TO VIEW
        // ---------------------------------------------------------
        $data = [
            'conference_id'     => $conferenceId,
            'selected_country'  => $selectedCountry,
            'countryList'       => $countryList,
            'countryToCurrency' => $countryToCurrency,
            'currencyPrices'    => $currencyPrices,
            'countryPrices'     => $countryPrices,
            'baseUSDPrice'      => $baseUSDPrice,
            'user_id'           => $userId,
            'currentPlan'       => (int)($session->get('plan') ?? 1),
        ];

        return module_view('Web', 'upgrade', $data);
    }

}
