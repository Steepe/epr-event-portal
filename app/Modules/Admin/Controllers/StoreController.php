<?php

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Services\EmergenceStoreService;

class StoreController extends BaseController
{
    public function index()
    {
        $store = new EmergenceStoreService();
        if (! $store->ready()) return module_view('Admin', 'store/index', ['ready' => false]);
        $db = db_connect();
        return module_view('Admin', 'store/index', [
            'ready' => true,
            'products' => $db->table('tbl_store_products')->orderBy('sort_order')->orderBy('product_id')->get()->getResultArray(),
            'coupons' => $db->table('tbl_store_coupons c')->select('c.*, p.name AS product_name')->join('tbl_store_products p', 'p.product_id = c.product_id', 'left')->orderBy('coupon_id', 'DESC')->get()->getResultArray(),
            'orders' => $db->table('tbl_store_orders o')->select('o.*, p.name AS product_name, u.email')->join('tbl_store_products p', 'p.product_id = o.product_id')->join('tbl_users u', 'u.id = o.user_id', 'left')->orderBy('order_id', 'DESC')->limit(50)->get()->getResultArray(),
            'entitlements' => $db->table('tbl_store_entitlements e')->select('e.*, p.name AS product_name, u.email')->join('tbl_store_products p', 'p.product_id = e.product_id')->join('tbl_users u', 'u.id = e.user_id', 'left')->orderBy('entitlement_id', 'DESC')->limit(50)->get()->getResultArray(),
            'videos' => $db->table('tbl_store_product_videos v')->select('v.*, p.name AS product_name')->join('tbl_store_products p', 'p.product_id = v.product_id')->orderBy('p.sort_order')->orderBy('v.sort_order')->get()->getResultArray(),
        ]);
    }

    public function saveProduct(?int $id = null)
    {
        if (! $this->ready()) return $this->notReady();
        $name = trim((string) $this->request->getPost('name'));
        $slug = strtolower(trim((string) $this->request->getPost('slug')));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug ?: $name);
        $slug = trim((string) $slug, '-');
        $price = (float) $this->request->getPost('price_amount');
        if ($name === '' || $slug === '' || $price < 0 || ! in_array($this->request->getPost('access_type'), ['replays', 'coaching_interest', 'external'], true)) {
            return redirect()->back()->withInput()->with('error', 'Add a name, valid price, and access type.');
        }
        $db = db_connect();
        if ($id && ! $db->table('tbl_store_products')->where('product_id', $id)->countAllResults()) {
            return redirect()->to(site_url('admin/store'))->with('error', 'That store product no longer exists.');
        }
        $duplicate = $db->table('tbl_store_products')->where('slug', $slug)->get()->getRowArray();
        if ($duplicate && (int) $duplicate['product_id'] !== (int) $id) return redirect()->back()->withInput()->with('error', 'That product URL slug is already used.');
        $data = [
            'slug' => $slug, 'name' => $name, 'description' => trim((string) $this->request->getPost('description')) ?: null,
            'access_type' => (string) $this->request->getPost('access_type'), 'access_url' => trim((string) $this->request->getPost('access_url')) ?: null,
            'price_amount' => $price, 'currency' => strtoupper(substr(trim((string) $this->request->getPost('currency')) ?: 'NGN', 0, 3)),
            'button_label' => trim((string) $this->request->getPost('button_label')) ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0, 'sort_order' => (int) $this->request->getPost('sort_order'), 'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($id) $db->table('tbl_store_products')->where('product_id', $id)->update($data); else { $data['created_at'] = date('Y-m-d H:i:s'); $db->table('tbl_store_products')->insert($data); }
        return redirect()->to(site_url('admin/store'))->with('success', 'Store product saved.');
    }

    public function saveCoupon(?int $id = null)
    {
        if (! $this->ready()) return $this->notReady();
        $code = strtoupper(preg_replace('/\s+/', '', trim((string) $this->request->getPost('code'))));
        $type = (string) $this->request->getPost('discount_type');
        $amount = (float) $this->request->getPost('discount_amount');
        if ($code === '' || ! in_array($type, ['fixed', 'percentage'], true) || $amount <= 0 || ($type === 'percentage' && $amount > 100)) return redirect()->back()->withInput()->with('error', 'Enter a valid code and discount.');
        $db = db_connect();
        if ($id && ! $db->table('tbl_store_coupons')->where('coupon_id', $id)->countAllResults()) {
            return redirect()->to(site_url('admin/store'))->with('error', 'That discount code no longer exists.');
        }
        $duplicate = $db->table('tbl_store_coupons')->where('code', $code)->get()->getRowArray();
        if ($duplicate && (int) $duplicate['coupon_id'] !== (int) $id) return redirect()->back()->withInput()->with('error', 'That discount code already exists.');
        $productId = $this->nullableInt('product_id');
        if ($productId !== null && ! $db->table('tbl_store_products')->where('product_id', $productId)->countAllResults()) {
            return redirect()->back()->withInput()->with('error', 'Choose a valid store product for this code.');
        }
        $data = ['product_id' => $productId, 'code' => $code, 'discount_type' => $type, 'discount_amount' => $amount, 'currency' => strtoupper(substr(trim((string) $this->request->getPost('currency')) ?: 'NGN', 0, 3)), 'starts_at' => $this->nullableDate('starts_at'), 'expires_at' => $this->nullableDate('expires_at'), 'max_redemptions' => $this->nullableInt('max_redemptions'), 'is_active' => $this->request->getPost('is_active') ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')];
        if ($id) $db->table('tbl_store_coupons')->where('coupon_id', $id)->update($data); else { $data['created_at'] = date('Y-m-d H:i:s'); $db->table('tbl_store_coupons')->insert($data); }
        return redirect()->to(site_url('admin/store'))->with('success', 'Discount code saved.');
    }

    public function saveVideo(?int $id = null)
    {
        if (! $this->ready()) return $this->notReady();
        $url = trim((string) $this->request->getPost('bunny_embed_url'));
        $productId = $this->nullableInt('product_id');
        if ($productId === null || trim((string) $this->request->getPost('title')) === '' || ! filter_var($url, FILTER_VALIDATE_URL)) return redirect()->back()->withInput()->with('error', 'Choose a product and add a title and valid Bunny embed URL.');
        $db = db_connect();
        if (! $db->table('tbl_store_products')->where('product_id', $productId)->where('access_type', 'replays')->countAllResults()) {
            return redirect()->back()->withInput()->with('error', 'Replay videos can only be linked to a replay-library product.');
        }
        if ($id && ! $db->table('tbl_store_product_videos')->where('video_id', $id)->countAllResults()) {
            return redirect()->to(site_url('admin/store'))->with('error', 'That replay video no longer exists.');
        }
        $data = ['product_id' => $productId, 'title' => trim((string) $this->request->getPost('title')), 'bunny_embed_url' => $url, 'sort_order' => (int) $this->request->getPost('sort_order'), 'is_active' => $this->request->getPost('is_active') ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')];
        if ($id) $db->table('tbl_store_product_videos')->where('video_id', $id)->update($data); else { $data['created_at'] = date('Y-m-d H:i:s'); $db->table('tbl_store_product_videos')->insert($data); }
        return redirect()->to(site_url('admin/store'))->with('success', 'Replay video saved.');
    }

    public function grant()
    {
        if (! $this->ready()) return $this->notReady();
        $email = strtolower(trim((string) $this->request->getPost('email'))); $productId = $this->nullableInt('product_id');
        $db = db_connect();
        $user = $db->table('tbl_users')->select('id')->where('email', $email)->get()->getRowArray();
        $product = $productId === null ? null : $db->table('tbl_store_products')->where('product_id', $productId)->get()->getRowArray();
        if (! $user || ! $product) return redirect()->back()->withInput()->with('error', 'Choose a valid attendee email and product.');
        (new EmergenceStoreService())->grantEntitlement((int) $user['id'], $productId, null, (int) session('admin_id'), trim((string) $this->request->getPost('notes')));
        return redirect()->to(site_url('admin/store'))->with('success', 'Access granted.');
    }

    public function revoke(int $id)
    {
        if (! $this->ready()) return $this->notReady();
        $row = db_connect()->table('tbl_store_entitlements')->where('entitlement_id', $id)->get()->getRowArray();
        if ($row) (new EmergenceStoreService())->revokeEntitlement((int) $row['user_id'], (int) $row['product_id']);
        return redirect()->to(site_url('admin/store'))->with('success', 'Access revoked.');
    }

    private function ready(): bool { return (new EmergenceStoreService())->ready(); }
    private function notReady() { return redirect()->to(site_url('admin/store'))->with('error', 'Run the Store migration before managing products.'); }
    private function nullableInt(string $key): ?int { $value = $this->request->getPost($key); return $value === null || $value === '' ? null : (int) $value; }
    private function nullableDate(string $key): ?string { $value = trim((string) $this->request->getPost($key)); return $value === '' ? null : date('Y-m-d H:i:s', strtotime($value)); }
}
