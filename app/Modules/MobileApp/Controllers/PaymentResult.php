<?php

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

/**
 * Payment result screens. The payment itself is confirmed by the gateway callback
 * before the attendee lands here, as on the desktop portal.
 */
class PaymentResult extends BaseController
{
    public function success(): string
    {
        return module_view('MobileApp', 'payment_result', [
            'paid'          => true,
            'reference'     => (string) $this->request->getGet('tx_ref'),
            'conference_id' => (int) ($this->request->getGet('conference_id') ?: session('live-conference-id')),
        ]);
    }

    public function cancelled(): string
    {
        return module_view('MobileApp', 'payment_result', [
            'paid'          => false,
            'reference'     => '',
            'conference_id' => 0,
        ]);
    }
}
