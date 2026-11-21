<?php

namespace App\Services;

use RuntimeException;

class EmergenceStoreService
{
    private const TABLES = ['tbl_store_products', 'tbl_store_coupons', 'tbl_store_orders', 'tbl_store_entitlements', 'tbl_store_product_videos'];

    public function ready(): bool
    {
        $db = db_connect();
        foreach (self::TABLES as $table) {
            if (! $db->tableExists($table)) {
                return false;
            }
        }
        return true;
    }

    public function activeProducts(): array
    {
        return db_connect()->table('tbl_store_products')
            ->where('is_active', 1)->orderBy('sort_order')->orderBy('product_id')->get()->getResultArray();
    }

    public function product(int $productId): ?array
    {
        $row = db_connect()->table('tbl_store_products')->where('product_id', $productId)->get()->getRowArray();
        return $row ?: null;
    }

    public static function effectiveProduct(array $product): array
    {
        // Existing coaching products were seeded as free before the paid offer launched.
        if (($product['access_type'] ?? '') === 'coaching_interest'
            && round((float) ($product['price_amount'] ?? 0), 2) === 0.0) {
            $product['price_amount'] = 19.99;
            $product['currency'] = 'USD';
        }

        return $product;
    }

    public function quote(array $product, string $code = ''): array
    {
        $product = self::effectiveProduct($product);
        $amount = round((float) $product['price_amount'], 2);
        $coupon = null;
        $code = strtoupper(trim($code));
        if ($code !== '') {
            $coupon = $this->validCoupon($code, (int) $product['product_id']);
            if (! $coupon) {
                throw new RuntimeException('That discount code is not valid for this product.');
            }
            $discount = $coupon['discount_type'] === 'percentage'
                ? $amount * ((float) $coupon['discount_amount'] / 100)
                : (float) $coupon['discount_amount'];
            $amount = max(0, round($amount - $discount, 2));
        }

        return ['amount' => $amount, 'currency' => strtoupper((string) $product['currency']), 'coupon' => $coupon];
    }

    public function createCheckout(array $product, int $userId, string $couponCode = '', ?string $successUrl = null, ?string $cancelUrl = null): string
    {
        $secret = trim((string) env('stripe.secretKey'));
        if ($secret === '') {
            throw new RuntimeException('Stripe is not configured yet. Please contact EPR Global.');
        }
        $quote = $this->quote($product, $couponCode);
        if ($quote['amount'] <= 0) {
            $this->grantFreeEntitlement($product, $userId, $quote['coupon']);
            return site_url('attendees/store/success?free=1&product=' . (int) $product['product_id']);
        }

        $db = db_connect();
        $now = date('Y-m-d H:i:s');
        $reference = 'epr-store-' . bin2hex(random_bytes(12));
        $db->table('tbl_store_orders')->insert([
            'order_ref' => $reference,
            'user_id' => $userId,
            'product_id' => (int) $product['product_id'],
            'coupon_id' => $quote['coupon']['coupon_id'] ?? null,
            'amount' => $quote['amount'],
            'currency' => $quote['currency'],
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $minorAmount = (int) round($quote['amount'] * $this->minorUnitMultiplier($quote['currency']));
        $params = [
            'mode' => 'payment',
            'success_url' => $successUrl ?: site_url('attendees/store/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => $cancelUrl ?: site_url('attendees/store?cancelled=1'),
            'client_reference_id' => $reference,
            'metadata[order_ref]' => $reference,
            'metadata[product_id]' => (string) $product['product_id'],
            'metadata[user_id]' => (string) $userId,
            'line_items[0][quantity]' => 1,
            'line_items[0][price_data][currency]' => strtolower($quote['currency']),
            'line_items[0][price_data][unit_amount]' => $minorAmount,
            'line_items[0][price_data][product_data][name]' => (string) $product['name'],
        ];
        $session = $this->stripeRequest('POST', 'checkout/sessions', $params, $secret);
        if (empty($session['url']) || empty($session['id'])) {
            throw new RuntimeException('Stripe could not create a checkout session.');
        }
        $db->table('tbl_store_orders')->where('order_ref', $reference)->update([
            'stripe_session_id' => (string) $session['id'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return (string) $session['url'];
    }

    public function completeStripeSession(array $session, string $payload): bool
    {
        if (($session['payment_status'] ?? '') !== 'paid') {
            return false;
        }
        $reference = (string) ($session['metadata']['order_ref'] ?? $session['client_reference_id'] ?? '');
        if ($reference === '') {
            throw new RuntimeException('Stripe webhook did not contain a store order reference.');
        }
        $db = db_connect();
        $db->transStart();
        $order = $db->table('tbl_store_orders')->where('order_ref', $reference)->get()->getRowArray();
        if (! $order) {
            $db->transComplete();
            throw new RuntimeException('Stripe webhook references an unknown store order.');
        }
        $markPaid = $db->table('tbl_store_orders')->where('order_id', $order['order_id'])->where('status !=', 'paid')->update([
            'status' => 'paid',
            'stripe_session_id' => (string) ($session['id'] ?? $order['stripe_session_id']),
            'stripe_payment_intent_id' => (string) ($session['payment_intent'] ?? ''),
            'provider_payload' => $payload,
            'paid_at' => $order['paid_at'] ?: date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $newlyPaid = $markPaid && $db->affectedRows() === 1;
        if ($newlyPaid) {
            $this->grantEntitlement((int) $order['user_id'], (int) $order['product_id'], (int) $order['order_id']);
            if (! empty($order['coupon_id'])) {
                $db->table('tbl_store_coupons')->where('coupon_id', (int) $order['coupon_id'])->set('redemption_count', 'redemption_count + 1', false)->update();
            }
        }
        $db->transComplete();
        if (! $db->transStatus()) {
            throw new RuntimeException('Unable to record the paid store order.');
        }
        if ($newlyPaid) {
            $this->sendPurchaseConfirmation($order);
        }
        return true;
    }

    public function verifyStripeSignature(string $payload, string $header): bool
    {
        $secret = trim((string) env('stripe.webhookSecret'));
        if ($secret === '' || $header === '') {
            return false;
        }
        $parts = [];
        foreach (explode(',', $header) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');
            $parts[$key][] = $value;
        }
        $timestamp = $parts['t'][0] ?? '';
        if (! ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300) {
            return false;
        }
        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        foreach ($parts['v1'] ?? [] as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }
        return false;
    }

    public function hasAccess(int $userId, int $productId): bool
    {
        return db_connect()->table('tbl_store_entitlements')->where('user_id', $userId)->where('product_id', $productId)->where('status', 'active')->countAllResults() > 0;
    }

    public function videos(int $productId): array
    {
        return db_connect()->table('tbl_store_product_videos')->where('product_id', $productId)->where('is_active', 1)->orderBy('sort_order')->orderBy('video_id')->get()->getResultArray();
    }

    public function grantEntitlement(int $userId, int $productId, ?int $orderId = null, ?int $adminId = null, string $notes = ''): void
    {
        $db = db_connect();
        $existing = $db->table('tbl_store_entitlements')->where('user_id', $userId)->where('product_id', $productId)->get()->getRowArray();
        $data = ['status' => 'active', 'order_id' => $orderId, 'granted_by_admin_id' => $adminId, 'notes' => $notes ?: null, 'granted_at' => date('Y-m-d H:i:s'), 'revoked_at' => null];
        if ($existing) {
            $db->table('tbl_store_entitlements')->where('entitlement_id', $existing['entitlement_id'])->update($data);
        } else {
            $data += ['user_id' => $userId, 'product_id' => $productId];
            $db->table('tbl_store_entitlements')->insert($data);
        }
    }

    public function revokeEntitlement(int $userId, int $productId): void
    {
        db_connect()->table('tbl_store_entitlements')->where('user_id', $userId)->where('product_id', $productId)->update(['status' => 'revoked', 'revoked_at' => date('Y-m-d H:i:s')]);
    }

    private function grantFreeEntitlement(array $product, int $userId, ?array $coupon): void
    {
        $this->grantEntitlement($userId, (int) $product['product_id']);
        if ($coupon) {
            db_connect()->table('tbl_store_coupons')->where('coupon_id', $coupon['coupon_id'])->set('redemption_count', 'redemption_count + 1', false)->update();
        }
    }

    private function sendPurchaseConfirmation(array $order): void
    {
        try {
            $recipient = db_connect()->table('tbl_users u')
                ->select('u.email, p.firstname, p.lastname')
                ->join('tbl_user_profiles p', 'p.user_id = u.id', 'left')
                ->where('u.id', (int) $order['user_id'])->get()->getRowArray();
            $product = $this->product((int) $order['product_id']);
            if (! $recipient || ! filter_var($recipient['email'], FILTER_VALIDATE_EMAIL) || ! $product) {
                throw new RuntimeException('Recipient or product was not found.');
            }

            $name = trim((string) ($recipient['firstname'] ?? '')) ?: 'there';
            $amount = strtoupper((string) $order['currency']) . ' ' . number_format((float) $order['amount'], 2);
            $accessUrl = site_url('attendees/store');
            $email = service('email');
            $email->clear(true);
            $email->setFrom(env('email.fromEmail') ?: 'no-reply@eprglobal.com', env('email.fromName') ?: 'EPR Global Events');
            $email->setTo((string) $recipient['email']);
            $email->setSubject('Your EPR Global purchase is confirmed');
            $email->setMailType('html');
            $email->setMessage(view('emails/store_purchase', [
                'name' => $name,
                'productName' => (string) $product['name'],
                'amount' => $amount,
                'reference' => (string) $order['order_ref'],
                'accessUrl' => $accessUrl,
            ]));
            if (! $email->send()) {
                throw new RuntimeException('Mail transport rejected the message.');
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Store purchase confirmation failed for order {reference}: {error}', [
                'reference' => (string) $order['order_ref'],
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function validCoupon(string $code, int $productId): ?array
    {
        $coupon = db_connect()->table('tbl_store_coupons')->where('code', $code)->where('is_active', 1)->get()->getRowArray();
        if (! $coupon || (! empty($coupon['product_id']) && (int) $coupon['product_id'] !== $productId)) return null;
        if ($coupon['discount_type'] === 'fixed' && ! empty($coupon['currency'])) {
            $product = $this->product($productId);
            if (! $product || strtoupper((string) $coupon['currency']) !== strtoupper((string) $product['currency'])) return null;
        }
        $now = date('Y-m-d H:i:s');
        if ((! empty($coupon['starts_at']) && $coupon['starts_at'] > $now) || (! empty($coupon['expires_at']) && $coupon['expires_at'] < $now)) return null;
        if ($coupon['max_redemptions'] !== null && (int) $coupon['redemption_count'] >= (int) $coupon['max_redemptions']) return null;
        return $coupon;
    }

    private function stripeRequest(string $method, string $path, array $params, string $secret): array
    {
        $curl = curl_init('https://api.stripe.com/v1/' . ltrim($path, '/'));
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $secret . ':',
        ]);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        $decoded = json_decode((string) $body, true);
        if ($body === false || $status < 200 || $status >= 300 || ! is_array($decoded)) {
            log_message('error', 'Stripe checkout request failed: {status} {error} {body}', ['status' => $status, 'error' => $error, 'body' => substr((string) $body, 0, 1000)]);
            throw new RuntimeException('Stripe could not start checkout. Please try again.');
        }
        return $decoded;
    }

    private function minorUnitMultiplier(string $currency): int
    {
        return in_array(strtoupper($currency), ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'], true) ? 1 : 100;
    }
}
