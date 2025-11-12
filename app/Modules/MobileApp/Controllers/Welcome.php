<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:14
 */


namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

class Welcome extends BaseController
{
    public function index()
    {
        // Ensure user is logged in before accessing welcome screen
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        return module_view('MobileApp', 'welcome');
    }
}
