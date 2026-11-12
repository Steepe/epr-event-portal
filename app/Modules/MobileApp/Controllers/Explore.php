<?php

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

/**
 * Explore tab: links to the screens that are not on the tab bar.
 */
class Explore extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $user = db_connect()->table('tbl_users')
            ->where('id', session('user_id'))
            ->get()
            ->getRowArray();

        return module_view('MobileApp', 'explore', [
            'premium' => (int) ($user['premium_access'] ?? 0),
        ]);
    }
}
