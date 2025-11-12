<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:13
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\AttendeeModel;

class Attendees extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $currentUserId = session('user_id');
        $search = $this->request->getGet('q');

        $model = new AttendeeModel();
        $attendees = $model->getAttendees($currentUserId, $search);

        return module_view('MobileApp', 'attendees_list', [
            'attendees' => $attendees,
            'search'    => $search,
        ]);
    }
}
