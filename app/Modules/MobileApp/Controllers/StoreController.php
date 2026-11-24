<?php

namespace App\Modules\MobileApp\Controllers;

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
        $entitledProductIds = $store->ready() ? $store->accessibleProductIds($products, (int) session('user_id')) : [];

        return module_view('MobileApp', 'store', [
            'ready' => $store->ready(),
            'offers' => StoreOfferCatalog::offers($products, $entitledProductIds, true),
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
        if (($product['access_type'] ?? '') === 'replays'
            && $store->hasReplayAccess((int) session('user_id'), $productId, $product)) {
            return redirect()->to($store->replaySessionsUrl($product, true));
        }

        try {
            $successUrl = site_url('mobile/store/success') . '?product=' . $productId . '&session_id={CHECKOUT_SESSION_ID}';
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
        $isReplay = ($product['access_type'] ?? '') === 'replays';
        $hasAccess = $productId > 0 && $store->ready() && ($isReplay
            ? $store->hasReplayAccess((int) session('user_id'), $productId, $product)
            : $store->hasAccess((int) session('user_id'), $productId));
        if ($isReplay && $hasAccess) {
            return redirect()->to($store->replaySessionsUrl($product, true))
                ->with('success', 'Your replay access is ready. Choose a session to watch.');
        }
        if ($isReplay && $this->request->getGet('free')) {
            return redirect()->to(site_url('mobile/store'))
                ->with('error', 'Free replay viewing has ended. Purchase access to keep watching.');
        }

        return module_view('MobileApp', 'store_success', [
            'hasAccess' => $hasAccess,
            'product' => $product,
            'isReplay' => $isReplay,
        ]);
    }

    public function replays(int $productId)
    {
        $store = new EmergenceStoreService();
        $product = $store->product($productId);
        if (! $product || ($product['access_type'] ?? '') !== 'replays') {
            throw PageNotFoundException::forPageNotFound();
        }
        if (! $store->hasReplayAccess((int) session('user_id'), $productId, $product)) {
            return redirect()->to(site_url('mobile/store'))->with('error', 'Purchase replay access to watch these videos.');
        }

        return module_view('MobileApp', 'store_replays', [
            'product' => $product,
            'videos' => $store->videos($productId),
        ]);
    }

}
