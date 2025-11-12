<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 16:11
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\Web\Models\AttendeeModel;

class Profile extends BaseController
{
    public function index()
    {
        $userId = session()->get('attendee_id');
        $model = new AttendeeModel();
        $data['profile'] = $model->find($userId);
        return module_view('MobileApp', 'profile/index', $data);
    }
}
