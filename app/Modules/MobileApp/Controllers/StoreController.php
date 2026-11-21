<?php

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Services\EmergenceStoreService;
use CodeIgniter\Exceptions\PageNotFoundException;

class StoreController extends BaseController
{
    public function index()
    {
        $store = new EmergenceStoreService();

        return module_view('MobileApp', 'store', [
            'ready' => $store->ready(),
            'products' => $store->ready() ? $store->activeProducts() : [],
            'entitledProductIds' => $store->ready() ? $this->entitledProductIds((int) session('user_id')) : [],
        ]);
    }

    public function checkout(int $productId)
    {
        $store = new EmergenceStoreService();
        if (! $store->ready()) {
            return redirect()->to(site_url('mobile/store'))->with('error', 'The store is being prepared. Please try again shortly.');
        }

        $product = $store->product($productId);
        if (! $product || empty($product['is_active'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        try {
            $successUrl = site_url('mobile/store/success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = site_url('mobile/store') . '?cancelled=1';
            $url = $store->createCheckout($product, (int) session('user_id'), '', $successUrl, $cancelUrl);

            return redirect()->to($url);
        } catch (\RuntimeException $exception) {
            return redirect()->to(site_url('mobile/store'))->with('error', $exception->getMessage());
        }
    }

    public function success()
    {
        $store = new EmergenceStoreService();
        $productId = (int) ($this->request->getGet('product') ?: 0);
        if ($productId < 1 && $store->ready() && ($stripeSessionId = trim((string) $this->request->getGet('session_id'))) !== '') {
            $order = db_connect()->table('tbl_store_orders')->where('stripe_session_id', $stripeSessionId)->get()->getRowArray();
            $productId = (int) ($order['product_id'] ?? 0);
        }

        $product = $productId > 0 ? $store->product($productId) : null;
        $hasAccess = $productId > 0 && $store->ready() && $store->hasAccess((int) session('user_id'), $productId);

        return module_view('MobileApp', 'store_success', [
            'hasAccess' => $hasAccess,
            'product' => $product,
        ]);
    }

    public function replays(int $productId)
    {
        $store = new EmergenceStoreService();
        $product = $store->product($productId);
        if (! $product || ($product['access_type'] ?? '') !== 'replays') {
            throw PageNotFoundException::forPageNotFound();
        }
        if (! $store->hasAccess((int) session('user_id'), $productId)) {
            return redirect()->to(site_url('mobile/store'))->with('error', 'Purchase replay access to watch these videos.');
        }

        return module_view('MobileApp', 'store_replays', [
            'product' => $product,
            'videos' => $store->videos($productId),
        ]);
    }

    private function entitledProductIds(int $userId): array
    {
        if ($userId < 1) {
            return [];
        }

        return array_map('intval', array_column(
            db_connect()->table('tbl_store_entitlements')->select('product_id')->where('user_id', $userId)->where('status', 'active')->get()->getResultArray(),
            'product_id'
        ));
    }
}
