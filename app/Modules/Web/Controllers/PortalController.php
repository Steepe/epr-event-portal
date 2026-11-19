<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Filters\MobileRedirect;
use CodeIgniter\HTTP\RedirectResponse;

class PortalController extends BaseController
{
    public function index(): RedirectResponse
    {
        // Phones get the mobile app; everything else gets the desktop portal.
        if (MobileRedirect::isPhone($this->request)) {
            $target = session()->get('isLoggedIn') ? 'mobile/welcome' : 'mobile/login';

            return redirect()->to(base_url($target))->setHeader('Vary', 'User-Agent');
        }

        $target = session()->has('attendee_id') ? 'attendees/start' : 'attendees/login';

        return redirect()->to(base_url($target));
    }
}
