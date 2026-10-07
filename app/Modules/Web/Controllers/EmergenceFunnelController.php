<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class EmergenceFunnelController extends BaseController
{
    public function index(): string
    {
        $checkoutUrl = env('funnel.emergence.checkoutUrl');
        if (! $checkoutUrl || str_contains($checkoutUrl, 'your-checkout-url')) {
            $checkoutUrl = '/emergence/checkout';
        }

        return module_view('Web', 'emergence_funnel', [
            'apiEndpoint' => '/emergence/funnel/register',
            'checkoutUrl' => $checkoutUrl,
            'upsellPrice' => env('funnel.emergence.upsellPrice') ?: '$50',
            'mailchimpTags' => implode(',', $this->mailchimpTags()),
            'countries' => $this->countries(),
            'showUpsell' => $this->envBoolean('funnel.emergence.showUpsell', false),
        ]);
    }

    private function countries(): array
    {
        try {
            return db_connect()
                ->table('tbl_countries')
                ->select('country_name')
                ->orderBy('country_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Emergence funnel country fetch failed: ' . $e->getMessage());

            return [];
        }
    }

    private function mailchimpTags(): array
    {
        $tags = array_merge(
            $this->splitTags(env('funnel.emergence.mailchimpTags') ?: 'emergence-registrant'),
            $this->splitTags(env('funnel.emergence.conferenceTag') ?: 'conference-2026')
        );

        return array_values(array_unique(array_filter($tags)));
    }

    private function splitTags(string $tags): array
    {
        return array_map('trim', explode(',', $tags));
    }

    private function envBoolean(string $key, bool $default): bool
    {
        $value = env($key);
        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
