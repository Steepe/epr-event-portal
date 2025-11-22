<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 13:42
 */

namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblCountryCurrencyModel;
use App\Modules\Api\Models\TblPremiumPricesModel;
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblUsersModel;
use App\Modules\Api\Models\TblAttendeePaymentsModel;
use CodeIgniter\Model;

/**
 * Universal Premium Access Payment Engine
 * - Currency auto-routing (Flutterwave / PayPal)
 * - Payment initialization
 * - Callback verification
 * - Premium activation (tbl_users)
 */
class PaymentsController extends BaseController
{
    use ResponseTrait;

    protected TblUsersModel $usersModel;
    protected TblAttendeePaymentsModel $paymentsModel;
    protected TblPremiumPricesModel $PremiumPrices;

    public function __construct()
    {
        $this->usersModel     = new TblUsersModel();
        $this->paymentsModel  = new TblAttendeePaymentsModel();
        $this->PremiumPrices  = new TblPremiumPricesModel();
    }

    /**
     * Initialize payment
     * POST: user_id, conference_id (optional)
     */
    public function initialize()
    {
        $userId       = $this->request->getPost('user_id');
        $conferenceId = $this->request->getPost('conference_id');

        if (!$userId) {
            return $this->fail('user_id is required', 400);
        }

        if (!$conferenceId) {
            return $this->fail('conference_id is required', 400);
        }

        // validate user
        $user = $this->usersModel->find($userId);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // validate conference
        $db = \Config\Database::connect();
        $conf = $db->table('tbl_conferences')
            ->where('conference_id', $conferenceId)
            ->get()->getRow();

        if (!$conf) {
            return $this->fail('Invalid conference_id', 400);
        }

        // resolve profile
        $profile = $db->table('tbl_user_profiles')->where('user_id', $userId)->get()->getRow();
        $country = $profile->country ?? 'NG';
        $currency = $this->resolveCurrency($country);
        $amount = $this->getPriceByCurrency($currency);

        $txRef = 'EPR-' . time() . '-' . rand(10000,99999);

        // insert pending payment
        $this->paymentsModel->insert([
            'attendee_id'   => $userId,
            'conference_id' => $conferenceId,
            'amount'        => $amount,
            'currency'      => $currency,
            'transid'       => $txRef,
            'payload'       => json_encode(['initialized' => true]),
            'paymentdate'   => date('Y-m-d H:i:s')
        ]);

        // route provider
        if ($currency === 'USD') {
            $redirect = $this->paypalInitialize($txRef, $amount);
        } else {
            $redirect = $this->flutterwaveInitialize($txRef, $amount, $currency, $user);
        }

        if (!$redirect) {
            return $this->fail('Provider initialization error', 500);
        }

        return $this->respond([
            'status'        => 'success',
            'redirect_url'  => $redirect,
            'tx_ref'        => $txRef
        ]);
    }

    /**
     * FLUTTERWAVE CALLBACK
     */
    public function flutterwaveCallback()
    {
        $txRef         = $this->request->getGet('tx_ref');
        $transactionId = $this->request->getGet('transaction_id');
        $status        = $this->request->getGet('status');

        if (!$txRef) {
            log_message('error', 'FW callback missing tx_ref.');
            return $this->fail('Missing tx_ref', 400);
        }

        if (!$transactionId) {
            log_message('error', "FW callback missing transaction_id for tx_ref: {$txRef}");
            return $this->fail('Missing transaction_id', 400);
        }

        // Retrieve pending payment record
        $payment = $this->paymentsModel
            ->where('transid', $txRef)
            ->first();

        if (!$payment) {
            log_message('error', "FW callback payment not found for tx_ref: {$txRef}");
            return $this->failNotFound('Payment not found');
        }

        $paymentId    = $payment['payment_id'];
        $attendeeId   = $payment['attendee_id'];
        $conferenceId = $payment['conference_id'];

        // Idempotency check
        if (!empty($payment['payload']) && strpos($payment['payload'], '"successful"') !== false) {
            log_message('info', "FW callback idempotent skip for tx_ref {$txRef}");
            return redirect()->to(
                base_url('attendees/payments/success?tx_ref=' . $txRef . '&conference_id=' . $conferenceId)
            );
        }

        // Verify with Flutterwave (CORRECT: use transaction ID)
        $verify = $this->flutterwaveVerify($transactionId);

        // Prepare unified update payload
        $updateData = [
            'payload'     => json_encode($verify, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'paymentdate' => date('Y-m-d H:i:s') // ALWAYS changes → NEVER empty update
        ];

        // FORCE UPDATE (NO MODEL → NO EXCEPTIONS)
        $db = \Config\Database::connect();
        $db->table('tbl_attendee_payments')
            ->where('payment_id', $paymentId)
            ->update($updateData);

        // Validate verification response
        if (
            !$verify ||
            !isset($verify['status']) ||
            strtolower($verify['status']) !== 'success' ||
            ($verify['data']['status'] ?? '') !== 'successful'
        ) {
            log_message('error', "FW verification failed for tx_ref: {$txRef}");
            return $this->fail('Payment verification failed', 400);
        }

        // Amount validation
        $expected = (float)$payment['amount'];
        $paid     = (float)($verify['data']['amount'] ?? 0);

        if ($paid < $expected) {
            log_message('error', "Amount mismatch for tx_ref {$txRef}. Expected {$expected}, got {$paid}");
            return $this->fail('Payment amount mismatch', 400);
        }

        // Activate premium access
        $this->activatePremium($attendeeId);

        log_message('info', "FW PAYMENT SUCCESS for tx_ref {$txRef}. Premium activated.");

        // Correct redirect
        return redirect()->to(
            base_url('attendees/payments/success?tx_ref=' . $txRef . '&conference_id=' . $conferenceId)
        );
    }

    /**
     * PAYPAL SUCCESS CALLBACK
     */
    public function paypalSuccess()
    {
        $orderId = $this->request->getGet('token');  // PayPal order
        if (!$orderId) {
            return $this->fail('Missing order token', 400);
        }

        // capture order
        $capture = $this->paypalCapture($orderId);

        if (!$capture || ($capture['status'] ?? '') !== 'COMPLETED') {
            return $this->fail('PayPal capture failed', 400);
        }

        // retrieve tx_ref stored in purchase_units
        $txRef = $capture['purchase_units'][0]['reference_id'] ?? null;

        if (!$txRef) {
            return $this->fail('Could not resolve tx_ref', 400);
        }

        $payment = $this->paymentsModel->where('transid', $txRef)->first();
        if (!$payment) {
            return $this->failNotFound('Payment record not found');
        }

        // update payment record
        $this->paymentsModel->update($payment['payment_id'], [
            'payload'       => json_encode($capture),
            'paymentdate'   => date('Y-m-d H:i:s')
        ]);

        // activate premium
        $this->activatePremium($payment['attendee_id']);

        return redirect()->to(base_url('payments/success?tx_ref=' . $txRef));
    }

    public function paypalCancel()
    {
        return redirect()->to(base_url('payments/cancelled'));
    }

    /**
     * Currency resolution
     */
    private function resolveCurrency($country)
    {
        $map = new TblCountryCurrencyModel();
        $row = $map->find($country);

        return $row['currency'] ?? 'USD';
    }

    private function getPriceByCurrency($currency): float
    {
        $PremiumPrices= $this->PremiumPrices;

        $row = $PremiumPrices->where('currency', $currency)
            ->where('active', 1)
            ->first();

        if ($row) {
            return (float) $row['amount'];
        }

        // fallback to USD if nothing exists
        $default = $PremiumPrices->where('currency', 'USD')->first();
        return (float) ($default['amount'] ?? 10);
    }

    /**
     * Flutterwave Initialize
     */
    private function flutterwaveInitialize($txRef, $amount, $currency, $user)
    {
        $payload = [
            'tx_ref'        => $txRef,
            'amount'        => (string)$amount,
            'currency'      => $currency,
            'redirect_url'  => base_url('api/payments/flutterwave-callback'),
            'customer' => [
                'email' => $user->email
            ],
            'customizations' => [
                'title' => 'Premium Access',
                'description' => 'Universal Premium Access Upgrade'
            ]
        ];

        $resp = $this->curlPost(
            'https://api.flutterwave.com/v3/payments',
            $payload,
            [
                'Authorization: Bearer ' . getenv('flutterwave.secret'),
                'Content-Type: application/json'
            ]
        );

        return $resp['data']['link'] ?? null;
    }

    private function flutterwaveVerify($transactionId)
    {
        return $this->curlGet(
            'https://api.flutterwave.com/v3/transactions/' . $transactionId . '/verify',
            [
                'Authorization: Bearer ' . getenv('flutterwave.secret'),
                'Content-Type: application/json'
            ]
        );
    }

    /**
     * PayPal Initialize
     */
    private function paypalInitialize($txRef, $amount)
    {
        $url = getenv('paypal.api') . '/v2/checkout/orders';

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $txRef,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($amount, 2, '.', '')
                ]
            ]],
            'application_context' => [
                'return_url' => base_url('api/payments/paypal-success'),
                'cancel_url' => base_url('api/payments/paypal-cancel')
            ]
        ];

        $token = $this->paypalAccessToken();

        $resp = $this->curlPost($url, $payload, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        foreach ($resp['links'] ?? [] as $l) {
            if ($l['rel'] === 'approve') {
                return $l['href'];
            }
        }

        return null;
    }

    private function paypalAccessToken()
    {
        $url = getenv('paypal.api') . '/v1/oauth2/token';
        $auth = base64_encode(getenv('paypal.client') . ':' . getenv('paypal.secret'));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $auth"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($res, true);
        return $json['access_token'] ?? null;
    }

    private function paypalCapture($orderId)
    {
        $url = getenv('paypal.api') . '/v2/checkout/orders/' . $orderId . '/capture';
        $token = $this->paypalAccessToken();

        return $this->curlPost($url, [], [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);
    }

    /**
     * Simple cURL helpers
     */
    private function curlPost($url, $payload, $headers)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }

    private function curlGet($url, $headers)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }

    /**
     * PREMIUM ACTIVATION
     * Updates tbl_users: premium_access = 1
     */
    private function activatePremium($userId)
    {
        log_message('info', "ACTIVATE PREMIUM CALLED for user_id: {$userId}");

        $db = \Config\Database::connect();

        $db->table('tbl_users')
            ->where('id', $userId)
            ->update(['premium_access' => 1]);

        return true;
    }

    /**
     * GET /api/payments/status/{user_id}
     */
    public function status($userId = null): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$userId) return $this->fail('user_id required', 400);
        $user = $this->usersModel->find($userId);
        if (!$user) return $this->failNotFound('User not found');

        return $this->respond([
            'user_id' => $userId,
            'premium' => (int)($user['premium_access'] ?? 0)
        ]);
    }

    /**
     * GET /api/payments/price?country={code|name}
     * Returns: { status, country, iso, currency, amount, gateway }
     */
    public function price()
    {
        $countryParam = $this->request->getGet('country');
        if (empty($countryParam)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'country is required'
            ], 400);
        }

        $db = \Config\Database::connect();

        // Normalize input
        $input = trim($countryParam);

        // 1) If input is 2 letter code, use it. Otherwise try to resolve name -> code using tbl_countries
        $iso = null;
        if (strlen($input) === 2) {
            $iso = strtoupper($input);
            // confirm exists in tbl_countries (optional)
            $row = $db->table('tbl_countries')->where('iso2', $iso)->get()->getRow();
            if (!$row) {
                // try matching name fallback
                $row2 = $db->table('tbl_countries')->like('name', $input)->get()->getRow();
                if ($row2) $iso = strtoupper($row2->iso2);
            }
        } else {
            // look up by name (case-insensitive)
            $row = $db->table('tbl_countries')->like('name', $input)->get()->getRow();
            if ($row) $iso = strtoupper($row->iso2);
        }

        // If still not found, try simple mapping from common names to codes
        if (!$iso) {
            $map = [
                'NIGERIA' => 'NG', 'GHANA' => 'GH', 'KENYA' => 'KE', 'SOUTH AFRICA' => 'ZA',
                'UNITED STATES' => 'US', 'UNITED KINGDOM' => 'GB', 'TANZANIA' => 'TZ',
                'UGANDA' => 'UG', 'ZAMBIA' => 'ZM'
            ];
            $up = strtoupper($input);
            $iso = $map[$up] ?? null;
        }

        // default to USD if still not resolvable
        if (!$iso) {
            $iso = 'US';
        }

        // 2) Resolve currency from tbl_country_currency (by iso code)
        $cc = $db->table('tbl_country_currency')->where('country', $iso)->get()->getRow();
        $currency = $cc->currency ?? 'USD';

        // 3) Try to find country-specific price in tbl_premium_prices (country column holds 2-letter iso)
        $priceRow = $db->table('tbl_premium_prices')
            ->where('active', 1)
            ->where('country', $iso)
            ->get()->getRow();

        // 4) If no country-specific price, find currency price
        if (!$priceRow) {
            $priceRow = $db->table('tbl_premium_prices')
                ->where('active', 1)
                ->where('currency', $currency)
                ->orderBy('id', 'ASC')
                ->get()->getRow();
        }

        // 5) Fallback to USD price if still nothing
        if (!$priceRow) {
            $priceRow = $db->table('tbl_premium_prices')
                ->where('active', 1)
                ->where('currency', 'USD')
                ->orderBy('id', 'ASC')
                ->get()->getRow();
        }

        $amount = $priceRow->amount ?? 20.00;

        // 6) Decide gateway
        $gateway = ($currency === 'USD') ? 'paypal' : 'flutterwave';

        return $this->respond([
            'status'   => 'success',
            'country'  => $input,
            'iso'      => $iso,
            'currency' => $currency,
            'amount'   => (float)$amount,
            'gateway'  => $gateway
        ]);
    }


}
