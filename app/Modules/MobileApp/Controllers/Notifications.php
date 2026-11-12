<?php

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Libraries\MobileNotifications;

class Notifications extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        return module_view('MobileApp', 'notifications', [
            'notifications' => MobileNotifications::all(),
        ]);
    }
}
