<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 20:29
 */

namespace App\Libraries;

class PaymentService
{
    protected $flwPublic;
    protected $flwSecret;
    protected $paypalClientId;
    protected $paypalSecret;

    public function __construct()
    {
        $this->flwPublic = getenv('FLW_PUBLIC_KEY');
        $this->flwSecret = getenv('FLW_SECRET_KEY');

        $this->paypalClientId = getenv('PAYPAL_CLIENT_ID');
        $this->paypalSecret = getenv('PAYPAL_SECRET');
    }

    public function createFlutterwavePayment(array $data)
    {
        // Create a payment/checkout link via Flutterwave's standard Checkout (v3) endpoint
        // For production, use server-to-server call and verify responses.

        // Example payload:
        $txref = 'epr-'.time().'-'.bin2hex(random_bytes(4));
        $payload = [
            'tx_ref' => $txref,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'redirect_url' => base_url('payments/flutterwave-return?txref='.$txref),
            'customer' => [
                'email' => $data['email'],
                'name' => trim(($data['firstname'] ?? '') . ' ' . ($data['lastname'] ?? ''))
            ],
            'meta' => ['attendee_id' => $data['attendee_id']],
        ];

        // call flutterwave API
        $client = \Config\Services::curlrequest(['verify' => false]);
        $res = $client->post('https://api.flutterwave.com/v3/payments', [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->flwSecret,
                'Content-Type' => 'application/json'
            ]
        ]);
        $body = json_decode($res->getBody(), true);
        if (isset($body['status']) && $body['status'] === 'success' && isset($body['data']['link'])) {
            return ['status'=>'ok','payment_link'=>$body['data']['link'],'txref'=>$txref];
        }

        return ['status'=>'error','message'=>'Failed to create flutterwave payment','raw'=>$body];
    }

    public function createPayPalPayment(array $data)
    {
        // Minimal: create a PayPal order server-side and return approval link (use PayPal v2 Orders API)
        // You must implement proper OAuth token retrieval and order creation.

        $accessToken = $this->paypalGetAccessToken();
        if (!$accessToken) return ['status'=>'error','message'=>'PayPal token failed'];

        $orderPayload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $data['currency'],
                        'value' => (string) $data['amount']
                    ],
                    'description' => 'EPR Global — Premium Access'
                ]
            ],
            'application_context' => [
                'return_url' => $data['return_url'],
                'cancel_url' => $data['cancel_url']
            ]
        ];

        $client = \Config\Services::curlrequest(['verify' => false]);
        $res = $client->post('https://api-m.paypal.com/v2/checkout/orders', [
            'json' => $orderPayload,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ]
        ]);
        $body = json_decode($res->getBody(), true);
        if (isset($body['links'])) {
            foreach ($body['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return ['status'=>'ok','checkout_url'=>$link['href'],'order_id'=>$body['id']];
                }
            }
        }
        return ['status'=>'error','message'=>'Failed to create paypal order','raw'=>$body];
    }

    protected function paypalGetAccessToken()
    {
        $client = \Config\Services::curlrequest(['verify' => false]);
        $res = $client->post('https://api-m.paypal.com/v1/oauth2/token', [
            'auth' => [$this->paypalClientId, $this->paypalSecret],
            'form_params' => ['grant_type' => 'client_credentials']
        ]);
        $body = json_decode($res->getBody(), true);
        return $body['access_token'] ?? null;
    }
}
