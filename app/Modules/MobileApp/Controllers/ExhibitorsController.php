<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 10:37
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\ExhibitorModel;
use App\Modules\MobileApp\Models\ExhibitorMessageModel;

class ExhibitorsController extends BaseController
{
    public function index()
    {
        $model = new ExhibitorModel();
        $exhibitors = $model->orderBy('company_name', 'ASC')->findAll();

        return module_view('MobileApp', 'exhibitors', ['exhibitors' => $exhibitors]);
    }

    public function sendMessage()
    {
        $request = service('request');
        $apiKey = $request->getHeaderLine('X-API-KEY');

        if ($apiKey !== env('api.securityKey')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $data = $request->getJSON(true);

        $messageModel = new ExhibitorMessageModel();
        $saved = $messageModel->insert([
            'attendee_id'  => $data['attendee_id'],
            'exhibitor_id' => $data['exhibitor_id'],
            'message'      => $data['message'],
            'is_from_exhibitor' => 0
        ]);

        if ($saved) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to send message']);
    }
}
