<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:08
 */


namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

class NetworkingCenter extends BaseController
{
    public function index()
    {
        // 🔒 Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        // Load the Networking Center view
        return module_view('MobileApp', 'networking_center', [
            'page_title' => 'Networking Center',
            'user_email' => session('user_email') ?? 'Guest',
        ]);
    }
}
