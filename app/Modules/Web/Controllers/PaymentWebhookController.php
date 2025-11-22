<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 20:31
 */

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblAttendeePaymentsModel;
use App\Modules\Api\Models\TblUsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentWebhookController extends BaseController
{
    protected $payments;
    protected $users;
    protected $db;

    public function __construct()
    {
        $this->payments = new TblAttendeePaymentsModel();
        $this->users    = new TblUsersModel();
        $this->db       = \Config\Database::connect();
    }

    /**
     * Endpoint for provider webhooks
     * Configure:
     *  - Flutterwave webhook -> /webhooks/flutterwave
     *  - PayPal webhook     -> /webhooks/paypal
     */
    public function flutterwave()
    {
        $payload = $this->request->getBody();
        $json = json_decode($payload, true);

        // Basic signature check (if you set one in Flutterwave dashboard, use it)
        // Flutterwave passes a signature header X-FLW-SIGNATURE when configured
        $signatureHeader = $this->request->getHeaderLine('X-FLW-SIGNATURE');
        // optionally verify using your secret
        // if ($signatureHeader !== hash_hmac('sha256', $payload, getenv('flutterwave.webhook_secret'))) { ... }

        // write audit
        $this->db->table('tbl_payment_audit')->insert([
            'transid'    => $json['data']['tx_ref'] ?? null,
            'provider'   => 'flutterwave',
            'payload'    => $payload,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $txRef = $json['data']['tx_ref'] ?? null;
        $status = $json['data']['status'] ?? ($json['event'] ?? null);

        if (!$txRef) {
            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST, 'Missing tx_ref');
        }

        $payment = $this->payments->where('transid', $txRef)->first();
        if (!$payment) {
            // still respond 200 to provider after logging — but optionally log for manual review
            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'No payment record');
        }

        if (in_array(strtolower($status), ['successful','success','completed'])) {
            // mark success
            $this->payments->update($payment['payment_id'], [
                'payload' => $payload,
                'paymentdate' => date('Y-m-d H:i:s'),
                // ensure transid stays same
            ]);

            // mark status column
            $this->db->table('tbl_attendee_payments')
                ->where('payment_id', $payment['payment_id'])
                ->update(['payload' => $payload, 'transid' => $txRef, 'paymentdate' => date('Y-m-d H:i:s')]);

            // activate premium on users table (user_id == attendee_id)
            $this->users->update($payment['attendee_id'], ['premium_access' => 1]);

            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'OK');
        } else {
            // mark failed/other
            $this->payments->update($payment['payment_id'], [
                'payload' => $payload
            ]);
            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'Ignored status');
        }
    }

    public function paypal()
    {
        $payload = $this->request->getBody();
        $json = json_decode($payload, true);

        // PayPal sends a transmission-id + signature headers for verification.
        // You should verify the webhook via PayPal's API (omitted here for brevity).
        $transmissionId = $this->request->getHeaderLine('Paypal-Transmission-Id');

        // write audit
        $this->db->table('tbl_payment_audit')->insert([
            'transid'    => $json['resource']['id'] ?? null,
            'provider'   => 'paypal',
            'payload'    => $payload,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // find matching payment by reference id in resource
        $referenceId = $json['resource']['purchase_units'][0]['reference_id'] ?? null;
        if (!$referenceId) {
            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'No reference');
        }

        $payment = $this->payments->where('transid', $referenceId)->first();
        if (!$payment) {
            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'No payment record');
        }

        // handle completed captures
        $event = strtolower($json['event'] ?? '');
        if (strpos($event, 'capture') !== false || $event === 'payment.captured' || $event === 'checkout.order.completed') {
            // mark payment and activate premium
            $this->payments->update($payment['payment_id'], [
                'payload' => $payload,
                'paymentdate' => date('Y-m-d H:i:s')
            ]);
            $this->users->update($payment['attendee_id'], ['premium_access' => 1]);
            return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'OK');
        }

        return $this->getResponse()->setStatusCode(ResponseInterface::HTTP_OK, 'Ignored event');
    }
}
