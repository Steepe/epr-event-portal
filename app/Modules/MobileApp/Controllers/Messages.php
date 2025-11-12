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
use App\Modules\Web\Models\MessageModel;

class Messages extends BaseController
{
    public function index()
    {
        $userId = session()->get('attendee_id');
        $model = new MessageModel();
        $data['threads'] = $model->getThreads($userId);
        return module_view('MobileApp', 'messages/index', $data);
    }

    public function thread($id)
    {
        $userId = session()->get('attendee_id');
        $model = new MessageModel();
        $data['messages'] = $model->getThreadMessages($userId, $id);
        return module_view('MobileApp', 'messages/thread', $data);
    }
}
