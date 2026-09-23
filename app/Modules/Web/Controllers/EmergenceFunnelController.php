<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class EmergenceFunnelController extends BaseController
{
    public function index(): string
    {
        return module_view('Web', 'emergence_funnel', [
            'apiEndpoint' => site_url('emergence/funnel/register'),
            'checkoutUrl' => env('funnel.emergence.checkoutUrl') ?: '#',
            'upsellPrice' => env('funnel.emergence.upsellPrice') ?: '$50',
            'mailchimpTags' => env('funnel.emergence.mailchimpTags') ?: 'emergence-registrant',
        ]);
    }
}
