<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class CheckoutController extends BaseController
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function emergence()
    {
        $email = strtolower(trim((string) $this->request->getGet('email')));
        $name = trim((string) $this->request->getGet('name'));
        $user = $email !== '' ? $this->findUserByEmail($email) : null;
        $attendee = $user ? $this->findAttendee((int) $user['id']) : null;
        $price = $this->priceFromString(env('funnel.emergence.upsellPrice') ?: '$50');

        if ($user && $attendee) {
            $name = trim(($attendee['firstname'] ?? '') . ' ' . ($attendee['lastname'] ?? '')) ?: $name;
        }

        return $this->showCheckout([
            'mode' => 'emergence',
            'productName' => 'VIP Ticket',
            'productDescription' => 'Upgrade your Emergence registration with VIP access.',
            'amount' => $price['amount'],
            'currency' => $price['currency'],
            'user' => $user,
            'attendee' => $attendee,
            'email' => $email,
            'name' => $name,
            'conferenceId' => $this->liveConferenceId(),
            'successUrl' => site_url('emergence/checkout/complete'),
            'loginRequired' => false,
        ]);
    }

    public function attendee()
    {
        $userId = (int) (session('user_id') ?: session('attendee_id'));
        if ($userId <= 0) {
            return redirect()->to(site_url('attendees/login'))->with('error', 'Please log in to continue checkout.');
        }

        $user = $this->findUserById($userId);
        $attendee = $this->findAttendee($userId);

        if (! $user || ! $attendee) {
            return redirect()->to(site_url('attendees/login'))->with('error', 'We could not find your attendee profile.');
        }

        $conferenceId = $this->liveConferenceId();
        $price = $this->attendeeTicketPrice($conferenceId, $attendee['country'] ?? '');

        return $this->showCheckout([
            'mode' => 'attendee',
            'productName' => 'Conference Ticket',
            'productDescription' => 'Complete your event payment to unlock the attendee portal.',
            'amount' => $price['amount'],
            'currency' => $price['currency'],
            'user' => $user,
            'attendee' => $attendee,
            'email' => $user['email'],
            'name' => trim(($attendee['firstname'] ?? '') . ' ' . ($attendee['lastname'] ?? '')),
            'conferenceId' => $conferenceId,
            'successUrl' => site_url('attendees/lobby'),
            'loginRequired' => true,
        ]);
    }

    public function verify()
    {
        $txRef = trim((string) $this->request->getGet('tx_ref'));
        $transactionId = trim((string) $this->request->getGet('transaction_id'));
        $status = strtolower(trim((string) $this->request->getGet('status')));

        if ($txRef === '' || $transactionId === '' || ! in_array($status, ['successful', 'completed'], true)) {
            return $this->checkoutResult(false, 'Payment was not completed.', null);
        }

        $payment = $this->db->table('tbl_payments')
            ->where('transaction_ref', $txRef)
            ->get()
            ->getRowArray();

        if (! $payment) {
            return $this->checkoutResult(false, 'We could not find this payment session.', null);
        }

        $metadata = json_decode((string) ($payment['metadata'] ?? '{}'), true) ?: [];
        if (($payment['status'] ?? '') === 'success') {
            return $this->checkoutResult(true, 'Payment confirmed. Thank you.', $payment, $metadata);
        }

        $result = $this->processFlutterwavePayment($payment, $transactionId, true);
        if (! $result['ok']) {
            return $this->checkoutResult(false, $result['message'], $payment);
        }

        return $this->checkoutResult(true, 'Payment confirmed. Thank you.', $payment, $metadata);
    }

    public function flutterwaveWebhook()
    {
        $secretHash = $this->flutterwaveWebhookSecret();
        if ($secretHash === '') {
            log_message('error', 'Flutterwave webhook received but flutterwave.webhook_secret is not configured.');

            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Webhook secret is not configured.',
            ]);
        }

        $signature = trim($this->request->getHeaderLine('verif-hash'));
        if ($signature === '' || ! hash_equals($secretHash, $signature)) {
            log_message('warning', 'Flutterwave webhook rejected because the verif-hash did not match.');

            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Invalid webhook signature.',
            ]);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = json_decode((string) $this->request->getBody(), true) ?: [];
        }

        $data = is_array($payload['data'] ?? null) ? $payload['data'] : $payload;
        $transactionId = trim((string) ($data['id'] ?? $data['transaction_id'] ?? $payload['id'] ?? $payload['transaction_id'] ?? ''));
        $txRef = trim((string) ($data['tx_ref'] ?? $payload['tx_ref'] ?? ''));
        $status = strtolower(trim((string) ($data['status'] ?? $payload['status'] ?? '')));

        if ($transactionId === '' || $txRef === '') {
            log_message('error', 'Flutterwave webhook missing transaction id or tx_ref: ' . json_encode($payload));

            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Missing transaction reference.',
            ]);
        }

        if ($status !== 'successful') {
            return $this->response->setJSON([
                'status' => 'ignored',
                'message' => 'Webhook was not a successful payment event.',
            ]);
        }

        $payment = $this->findPaymentByReference($txRef);
        if (! $payment) {
            log_message('warning', 'Flutterwave webhook had no matching payment for tx_ref: ' . $txRef);

            return $this->response->setJSON([
                'status' => 'ignored',
                'message' => 'No matching payment session.',
            ]);
        }

        if (($payment['status'] ?? '') === 'success') {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment was already processed.',
            ]);
        }

        $result = $this->processFlutterwavePayment($payment, $transactionId, false);
        if (! $result['ok']) {
            log_message('error', 'Flutterwave webhook verification failed: ' . json_encode($result));

            return $this->response->setStatusCode(502)->setJSON([
                'status' => 'error',
                'message' => $result['message'],
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Payment confirmed.',
        ]);
    }

    public function createPaypalOrder()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        $paymentId = (int) ($data['payment_id'] ?? 0);
        $payment = $paymentId > 0 ? $this->findPendingPayment($paymentId) : null;

        if (! $payment) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Payment session not found.',
            ]);
        }

        if (! $this->paypalIsConfigured()) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'PayPal is not configured.',
            ]);
        }

        if (! $this->paypalSupportsCurrency((string) $payment['currency'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'PayPal is not available for this currency.',
            ]);
        }

        $metadata = json_decode((string) ($payment['metadata'] ?? '{}'), true) ?: [];
        $order = $this->paypalRequest('POST', '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $payment['transaction_ref'],
                'description' => $metadata['product_name'] ?? 'EPR Global checkout',
                'amount' => [
                    'currency_code' => strtoupper((string) $payment['currency']),
                    'value' => $this->paypalAmountValue((float) $payment['amount'], (string) $payment['currency']),
                ],
            ]],
        ]);

        if (! $order['ok'] || empty($order['body']['id'])) {
            log_message('error', 'PayPal order create failed: ' . json_encode($order));

            return $this->response->setStatusCode(502)->setJSON([
                'status' => 'error',
                'message' => 'Unable to create PayPal order.',
            ]);
        }

        $metadata['paypal_order_id'] = $order['body']['id'];
        $this->db->table('tbl_payments')
            ->where('payment_id', $payment['payment_id'])
            ->update([
                'provider' => 'paypal',
                'metadata' => json_encode($metadata),
            ]);

        return $this->response->setJSON([
            'status' => 'success',
            'orderID' => $order['body']['id'],
        ]);
    }

    public function capturePaypalOrder()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        $paymentId = (int) ($data['payment_id'] ?? 0);
        $orderId = trim((string) ($data['order_id'] ?? ''));
        $payment = $paymentId > 0 ? $this->findPendingPayment($paymentId) : null;

        if (! $payment || $orderId === '') {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Payment session not found.',
            ]);
        }

        if (! $this->paypalIsConfigured()) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'PayPal is not configured.',
            ]);
        }

        if (! $this->paypalSupportsCurrency((string) $payment['currency'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'PayPal is not available for this currency.',
            ]);
        }

        $metadata = json_decode((string) ($payment['metadata'] ?? '{}'), true) ?: [];
        if (($metadata['paypal_order_id'] ?? '') !== $orderId) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'PayPal order does not match this payment session.',
            ]);
        }

        $capture = $this->paypalRequest('POST', '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture', []);
        if (! $capture['ok'] || strtoupper((string) ($capture['body']['status'] ?? '')) !== 'COMPLETED') {
            $this->markPaymentFailed((int) $payment['payment_id'], $orderId, $capture, 'paypal');
            log_message('error', 'PayPal capture failed: ' . json_encode($capture));

            return $this->response->setStatusCode(502)->setJSON([
                'status' => 'error',
                'message' => 'Unable to capture PayPal payment.',
            ]);
        }

        $captureData = $capture['body'];
        $purchaseUnit = $captureData['purchase_units'][0] ?? [];
        $paymentCapture = $purchaseUnit['payments']['captures'][0] ?? [];
        $captureId = (string) ($paymentCapture['id'] ?? $orderId);
        $amount = $paymentCapture['amount'] ?? [];
        $paidAmount = (float) ($amount['value'] ?? 0);
        $paidCurrency = strtoupper((string) ($amount['currency_code'] ?? ''));

        if (
            abs($paidAmount - (float) $payment['amount']) > 0.01
            || $paidCurrency !== strtoupper((string) $payment['currency'])
        ) {
            $this->markPaymentFailed((int) $payment['payment_id'], $captureId, $capture, 'paypal');

            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'PayPal amount verification failed.',
            ]);
        }

        $this->recordSuccessfulPayment($payment, $captureId, $capture, 'paypal');

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Payment confirmed.',
            'redirect_url' => $metadata['success_url'] ?? site_url('attendees/lobby'),
        ]);
    }

    public function complete()
    {
        return $this->checkoutResult(true, 'Registration complete. Thank you.', null);
    }

    private function showCheckout(array $data)
    {
        if (! $data['user'] || ! $data['attendee']) {
            return module_view('Web', 'checkout', [
                'canPay' => false,
                'message' => 'We could not find this registration. Please complete the registration form first.',
                'checkout' => $data,
                'publicKey' => '',
            ]);
        }

        $payment = $this->createPendingPayment($data);

        return module_view('Web', 'checkout', [
            'canPay' => true,
            'message' => '',
            'checkout' => $data + $payment,
            'publicKey' => $this->flutterwavePublicKey(),
            'paypalClientId' => $this->paypalClientId(),
            'paypalConfigured' => $this->paypalIsConfigured() && $this->paypalSupportsCurrency((string) $data['currency']),
        ]);
    }

    private function createPendingPayment(array $data): array
    {
        $txRef = 'epr-' . date('ymdHis') . '-' . bin2hex(random_bytes(4));
        $metadata = [
            'mode' => $data['mode'],
            'product_name' => $data['productName'],
            'attendee_id' => (int) $data['user']['id'],
            'conference_id' => $data['conferenceId'],
            'email' => $data['email'],
            'name' => $data['name'],
            'success_url' => $data['successUrl'],
        ];

        $this->db->table('tbl_payments')->insert([
            'user_id' => (int) $data['user']['id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => 'pending',
            'provider' => 'checkout',
            'transaction_ref' => $txRef,
            'metadata' => json_encode($metadata),
        ]);

        return [
            'txRef' => $txRef,
            'paymentId' => (int) $this->db->insertID(),
        ];
    }

    private function processFlutterwavePayment(array $payment, string $transactionId, bool $markFailed): array
    {
        $verification = $this->verifyFlutterwaveTransaction($transactionId);
        if (! $verification['ok']) {
            if ($markFailed) {
                $this->markPaymentFailed((int) $payment['payment_id'], $transactionId, $verification);
            }

            return [
                'ok' => false,
                'message' => 'Payment verification failed. Please contact support.',
                'verification' => $verification,
            ];
        }

        $data = $verification['data'] ?? [];
        $expectedTxRef = (string) $payment['transaction_ref'];
        $verifiedTxRef = (string) ($data['tx_ref'] ?? '');
        $expectedAmount = (float) $payment['amount'];
        $expectedCurrency = strtoupper((string) $payment['currency']);
        $paidAmount = (float) ($data['amount'] ?? 0);
        $paidCurrency = strtoupper((string) ($data['currency'] ?? ''));

        if (
            $verifiedTxRef !== $expectedTxRef
            || abs($paidAmount - $expectedAmount) > 0.01
            || $paidCurrency !== $expectedCurrency
        ) {
            if ($markFailed) {
                $this->markPaymentFailed((int) $payment['payment_id'], $transactionId, $verification);
            }

            return [
                'ok' => false,
                'message' => 'Payment amount verification failed. Please contact support.',
                'verification' => $verification,
            ];
        }

        $this->recordSuccessfulPayment($payment, $transactionId, $verification);

        return [
            'ok' => true,
            'message' => 'Payment confirmed.',
            'verification' => $verification,
        ];
    }

    private function recordSuccessfulPayment(array $payment, string $transactionId, array $verification, string $provider = 'flutterwave'): void
    {
        $metadata = json_decode((string) ($payment['metadata'] ?? '{}'), true) ?: [];
        $userId = (int) ($payment['user_id'] ?? 0);
        $conferenceId = $metadata['conference_id'] ?? null;

        $this->db->table('tbl_payments')
            ->where('payment_id', $payment['payment_id'])
            ->update([
                'status' => 'success',
                'provider' => $provider,
                'metadata' => json_encode($metadata + ['verification' => $verification['data']]),
            ]);

        $exists = $this->db->table('tbl_attendee_payments')
            ->where('transid', $transactionId)
            ->get()
            ->getRowArray();

        if (! $exists && $userId > 0) {
            $this->db->table('tbl_attendee_payments')->insert([
                'attendee_id' => $userId,
                'conference_id' => $conferenceId,
                'amount' => $payment['amount'],
                'currency' => $payment['currency'],
                'transid' => $transactionId,
                'payload' => json_encode($verification['data']),
                'paymentdate' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->table('tbl_payment_audit')->insert([
            'payment_id' => $payment['payment_id'],
            'transid' => $transactionId,
            'provider' => $provider,
            'payload' => json_encode($verification),
        ]);
    }

    private function markPaymentFailed(int $paymentId, string $transactionId, array $verification, string $provider = 'flutterwave'): void
    {
        $this->db->table('tbl_payments')
            ->where('payment_id', $paymentId)
            ->update([
                'status' => 'failed',
                'provider' => $provider,
            ]);

        $this->db->table('tbl_payment_audit')->insert([
            'payment_id' => $paymentId,
            'transid' => $transactionId,
            'provider' => $provider,
            'payload' => json_encode($verification),
        ]);
    }

    private function checkoutResult(bool $success, string $message, ?array $payment, array $metadata = [])
    {
        return module_view('Web', 'checkout_result', [
            'success' => $success,
            'message' => $message,
            'payment' => $payment,
            'metadata' => $metadata,
        ]);
    }

    private function verifyFlutterwaveTransaction(string $transactionId): array
    {
        $secret = (string) env('flutterwave.secret');
        if ($secret === '') {
            return ['ok' => false, 'error' => 'Flutterwave secret key is not configured.'];
        }

        $ch = curl_init('https://api.flutterwave.com/v3/transactions/' . rawurlencode($transactionId) . '/verify');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret],
            CURLOPT_TIMEOUT => 15,
        ]);
        $rawBody = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $body = is_string($rawBody) ? json_decode($rawBody, true) : null;
        $isSuccessful = $httpCode >= 200
            && $httpCode < 300
            && ($body['status'] ?? null) === 'success'
            && ($body['data']['status'] ?? null) === 'successful';

        return [
            'ok' => $error === '' && $isSuccessful,
            'http_code' => $httpCode,
            'error' => $error ?: null,
            'data' => $body['data'] ?? null,
            'body' => $body,
        ];
    }

    private function paypalRequest(string $method, string $path, array $payload): array
    {
        $token = $this->paypalAccessToken();
        if (! $token['ok']) {
            return $token;
        }

        $ch = curl_init(rtrim($this->paypalApiBase(), '/') . $path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token['access_token'],
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        if ($payload !== []) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $rawBody = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $body = is_string($rawBody) && $rawBody !== '' ? json_decode($rawBody, true) : null;

        return [
            'ok' => $error === '' && $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'error' => $error ?: null,
            'body' => $body,
            'data' => $body,
        ];
    }

    private function paypalAccessToken(): array
    {
        $clientId = $this->paypalClientId();
        $secret = $this->paypalSecret();

        if ($clientId === '' || $secret === '') {
            return ['ok' => false, 'error' => 'PayPal credentials are not configured.'];
        }

        $ch = curl_init(rtrim($this->paypalApiBase(), '/') . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $clientId . ':' . $secret,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Accept-Language: en_US'],
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_TIMEOUT => 15,
        ]);
        $rawBody = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $body = is_string($rawBody) ? json_decode($rawBody, true) : null;

        return [
            'ok' => $error === '' && $httpCode >= 200 && $httpCode < 300 && ! empty($body['access_token']),
            'http_code' => $httpCode,
            'error' => $error ?: null,
            'access_token' => $body['access_token'] ?? null,
            'body' => $body,
        ];
    }

    private function attendeeTicketPrice(?int $conferenceId, string $country): array
    {
        $ticket = $conferenceId
            ? $this->db->table('tbl_ticket_prices')->where('conference_id', $conferenceId)->get()->getRowArray()
            : null;

        if (! $ticket) {
            return $this->priceFromString(env('funnel.emergence.upsellPrice') ?: '$50');
        }

        return match (strtolower($country)) {
            'nigeria' => ['amount' => (float) $ticket['amount_naira'], 'currency' => 'NGN'],
            'kenya' => ['amount' => (float) $ticket['amount_shillings'], 'currency' => 'KES'],
            'south africa' => ['amount' => (float) $ticket['amount_rands'], 'currency' => 'ZAR'],
            default => ['amount' => (float) $ticket['amount_dollar'], 'currency' => 'USD'],
        };
    }

    private function priceFromString(string $price): array
    {
        $currency = str_contains($price, '₦') ? 'NGN' : 'USD';
        if (preg_match('/\b(NGN|USD|KES|ZAR|GHS|UGX|TZS|ZMW)\b/i', $price, $matches)) {
            $currency = strtoupper($matches[1]);
        }

        $amount = (float) preg_replace('/[^0-9.]/', '', $price);
        return ['amount' => $amount > 0 ? $amount : 50.00, 'currency' => $currency];
    }

    private function flutterwavePublicKey(): string
    {
        return (string) (env('flutterwave.public') ?: env('flutterwave.public_key') ?: 'FLWPUBK-81b14e73cc85cb37c3470031779b303d-X');
    }

    private function flutterwaveWebhookSecret(): string
    {
        $secret = trim((string) env('flutterwave.webhook_secret'));
        $secret = preg_replace('/\s+#.*$/', '', $secret) ?: '';

        return str_contains($secret, 'OPTIONAL_WEBHOOK_SECRET') ? '' : $secret;
    }

    private function paypalClientId(): string
    {
        $clientId = trim((string) env('paypal.client'));

        return str_contains($clientId, 'PAYPAL_CLIENT_ID') ? '' : $clientId;
    }

    private function paypalSecret(): string
    {
        $secret = trim((string) env('paypal.secret'));

        return str_contains($secret, 'PAYPAL_SECRET') ? '' : $secret;
    }

    private function paypalApiBase(): string
    {
        $api = trim((string) env('paypal.api'));
        $api = preg_replace('/\s+#.*$/', '', $api) ?: 'https://api-m.sandbox.paypal.com';

        return rtrim($api, '/');
    }

    private function paypalIsConfigured(): bool
    {
        return $this->paypalClientId() !== '' && $this->paypalSecret() !== '';
    }

    private function paypalSupportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), [
            'AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF',
            'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN',
            'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD',
        ], true);
    }

    private function paypalAmountValue(float $amount, string $currency): string
    {
        return in_array(strtoupper($currency), ['HUF', 'JPY', 'TWD'], true)
            ? number_format($amount, 0, '.', '')
            : number_format($amount, 2, '.', '');
    }

    private function findPendingPayment(int $paymentId): ?array
    {
        return $this->db->table('tbl_payments')
            ->where('payment_id', $paymentId)
            ->where('status', 'pending')
            ->get()
            ->getRowArray();
    }

    private function findPaymentByReference(string $txRef): ?array
    {
        return $this->db->table('tbl_payments')
            ->where('transaction_ref', $txRef)
            ->get()
            ->getRowArray();
    }

    private function liveConferenceId(): ?int
    {
        $conference = $this->db->table('tbl_conferences')
            ->where('status', 'live')
            ->orderBy('conference_id', 'DESC')
            ->get()
            ->getRowArray();

        return $conference ? (int) $conference['conference_id'] : null;
    }

    private function findUserByEmail(string $email): ?array
    {
        return $this->db->table('tbl_users')->where('email', $email)->get()->getRowArray();
    }

    private function findUserById(int $id): ?array
    {
        return $this->db->table('tbl_users')->where('id', $id)->get()->getRowArray();
    }

    private function findAttendee(int $userId): ?array
    {
        return $this->db->table('tbl_attendees')->where('attendee_id', $userId)->get()->getRowArray();
    }
}
