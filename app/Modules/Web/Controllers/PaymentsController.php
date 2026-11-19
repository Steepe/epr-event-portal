<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 16:03
 */

namespace App\Modules\Web\Controllers;


use App\Controllers\BaseController;

class PaymentsController extends BaseController
{

    public function upgrade($sessionId = null)
    {
        return module_view('Web', 'upgrade', [
            'sessionId' => $sessionId
        ]);
    }

    public function success(): string
    {
        $txRef = $this->request->getGet('tx_ref');
        return module_view('Web', 'success', ['tx_ref' => $txRef]);
    }

    public function cancelled(): string
    {
        return module_view('Web', 'cancelled');
    }

}