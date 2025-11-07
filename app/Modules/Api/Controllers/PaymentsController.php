<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 13:42
 */


namespace App\Modules\Api\Controllers;

use App\Modules\Api\Models\TblAttendeePaymentsModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class PaymentsController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblAttendeePaymentsModel::class;
    protected string $format    = 'json';

    /**
     * GET /api/payments/check/{attendee_id}
     * Checks if an attendee has an active or any payment for the current conference
     */
    public function check($attendee_id = null)
    {
        if (!$attendee_id) {
            return $this->failValidationErrors('Missing attendee ID.');
        }

        $conferenceId = $this->request->getGet('conference_id') ?? 1; // Default or pass as ?conference_id=

        $paymentModel = new TblAttendeePaymentsModel();

        $payment = $paymentModel
            ->where('attendee_id', $attendee_id)
            ->where('conference_id', $conferenceId)
            ->orderBy('paymentdate', 'DESC')
            ->first();

        if (!$payment) {
            return $this->respond([
                'status' => 'success',
                'data'   => [],
                'message' => 'No payment record found'
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $payment
        ]);
    }
}
