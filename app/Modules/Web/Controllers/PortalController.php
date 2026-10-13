<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class PortalController extends BaseController
{
    public function index(): RedirectResponse
    {
        $target = session()->has('attendee_id') ? 'attendees/start' : 'attendees/login';

        return redirect()->to(base_url($target));
    }
}
