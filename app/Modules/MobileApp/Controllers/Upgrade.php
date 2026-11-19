<?php

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Services\PortalAccessService;

/**
 * Premium Access upgrade. Prices and gateways follow the desktop portal:
 * a country price wins over a currency price, which wins over the USD base price.
 */
class Upgrade extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $db     = db_connect();
        $userId = (int) session('user_id');

        $conferenceId = (int) (session('live-conference-id') ?: ((new PortalAccessService())->liveConferenceId() ?? 0));

        $countries = [];
        foreach ($db->table('tbl_countries')->orderBy('country_name', 'ASC')->get()->getResultArray() as $row) {
            $code = strtoupper((string) $row['country_code']);
            if ($code === '') {
                continue;
            }
            $countries[$code] = [
                'name'     => (string) $row['country_name'],
                'currency' => strtoupper((string) ($row['currency_code'] ?? '') ?: 'USD'),
            ];
        }

        $attendee = $db->table('tbl_attendees')->select('country')->where('attendee_id', $userId)->get()->getRowArray();
        $selected = $this->resolveCountry((string) ($attendee['country'] ?? ''), $countries) ?? array_key_first($countries);

        $currencyPrices = [];
        $countryPrices  = [];
        $priceRows      = $db->tableExists('tbl_premium_prices')
            ? $db->table('tbl_premium_prices')->where('active', 1)->get()->getResultArray()
            : [];
        foreach ($priceRows as $price) {
            if (!empty($price['country'])) {
                $countryPrices[strtoupper((string) $price['country'])] = (float) $price['amount'];
            } else {
                $currencyPrices[strtoupper((string) $price['currency'])] = (float) $price['amount'];
            }
        }

        $user = $db->table('tbl_users')->where('id', $userId)->get()->getRowArray();

        return module_view('MobileApp', 'upgrade', [
            'conference_id'    => $conferenceId,
            'user_id'          => $userId,
            'countries'        => $countries,
            'selected_country' => $selected,
            'currencyPrices'   => $currencyPrices,
            'countryPrices'    => $countryPrices,
            'baseUSDPrice'     => $currencyPrices['USD'] ?? 20,
            'premium'          => (int) ($user['premium_access'] ?? 0),
        ]);
    }

    /**
     * Attendee countries are stored either as an ISO code or as a full name.
     *
     * @param array<string, array{name: string, currency: string}> $countries
     */
    private function resolveCountry(string $value, array $countries): ?string
    {
        $value = strtoupper(trim($value));

        if ($value === '') {
            return null;
        }
        if (isset($countries[$value])) {
            return $value;
        }

        foreach ($countries as $code => $country) {
            if (strtoupper($country['name']) === $value) {
                return $code;
            }
        }

        return null;
    }
}
