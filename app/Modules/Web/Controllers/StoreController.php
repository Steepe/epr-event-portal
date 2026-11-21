<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Services\EmergenceStoreService;
use App\Services\StoreOfferCatalog;
use CodeIgniter\Exceptions\PageNotFoundException;

class StoreController extends BaseController
{
    public function index()
    {
        $store = new EmergenceStoreService();
        $products = $store->ready() ? $store->activeProducts() : [];
        $entitledProductIds = $store->ready() ? $this->entitledProductIds((int) session('user_id')) : [];

        return module_view('Web', 'store/index', [
            'ready' => $store->ready(),
            'offers' => StoreOfferCatalog::offers($products, $entitledProductIds),
        ]);
    }

    public function checkout(int $productId)
    {
        $store = new EmergenceStoreService();
        if (! $store->ready()) return redirect()->to(site_url('attendees/store'))->with('error', 'The store is being prepared. Please try again shortly.');
        $product = $store->product($productId);
        if (! $product || empty($product['is_active'])) throw PageNotFoundException::forPageNotFound();
        try {
            $url = $store->createCheckout($product, (int) session('user_id'), (string) $this->request->getPost('coupon_code'));
            return redirect()->to($url);
        } catch (\RuntimeException $exception) {
            return redirect()->to(site_url('attendees/store'))->withInput()->with('error', $exception->getMessage());
        }
    }

    public function success()
    {
        $store = new EmergenceStoreService();
        $productId = (int) ($this->request->getGet('product') ?: 0);
        if ($productId < 1 && ($sessionId = trim((string) $this->request->getGet('session_id'))) !== '') {
            $order = db_connect()->table('tbl_store_orders')->where('stripe_session_id', $sessionId)->get()->getRowArray();
            $productId = (int) ($order['product_id'] ?? 0);
        }
        $hasAccess = $productId > 0 && $store->ready() && $store->hasAccess((int) session('user_id'), $productId);
        return module_view('Web', 'store/success', ['hasAccess' => $hasAccess, 'product' => $productId > 0 ? $store->product($productId) : null]);
    }

    public function replayLibrary(int $productId)
    {
        $store = new EmergenceStoreService();
        $product = $store->product($productId);
        if (! $product || ($product['access_type'] ?? '') !== 'replays') throw PageNotFoundException::forPageNotFound();
        if (! $store->hasAccess((int) session('user_id'), $productId)) {
            return redirect()->to(site_url('attendees/store'))->with('error', 'Purchase replay access to watch these videos.');
        }
        return module_view('Web', 'store/replays', ['product' => $product, 'videos' => $store->videos($productId)]);
    }

    public function stripeWebhook()
    {
        $payload = $this->request->getBody();
        $store = new EmergenceStoreService();
        if (! $store->verifyStripeSignature($payload, $this->request->getHeaderLine('Stripe-Signature'))) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid Stripe signature.']);
        }
        $event = json_decode($payload, true);
        if (! is_array($event)) return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid payload.']);
        try {
            if (($event['type'] ?? '') === 'checkout.session.completed') {
                $store->completeStripeSession((array) ($event['data']['object'] ?? []), $payload);
            }
            return $this->response->setJSON(['received' => true]);
        } catch (\Throwable $exception) {
            log_message('error', 'Stripe store webhook failed: ' . $exception->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to process event.']);
        }
    }

    private function entitledProductIds(int $userId): array
    {
        if ($userId < 1) return [];
        return array_map('intval', array_column(db_connect()->table('tbl_store_entitlements')->select('product_id')->where('user_id', $userId)->where('status', 'active')->get()->getResultArray(), 'product_id'));
    }
}
