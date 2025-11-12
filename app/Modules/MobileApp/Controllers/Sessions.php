<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 16:10
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\Web\Models\SessionModel;

class Sessions extends BaseController
{
    public function index()
    {
        $sessionModel = new SessionModel();
        $data['sessions'] = $sessionModel->orderBy('start_time','ASC')->findAll();
        return module_view('MobileApp', 'sessions/index', $data);
    }

    public function view($id)
    {
        $sessionModel = new SessionModel();
        $session = $sessionModel->find($id);

        if (!$session) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Session not found');
        }

        $data['session'] = $session;
        return module_view('MobileApp', 'sessions/view', $data);
    }
}
