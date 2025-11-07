<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 01/11/2025
 * Time: 21:14
 */


namespace App\Modules\Api\Controllers;

use App\Modules\Api\Models\TblExhibitorsModel;
use App\Modules\Api\Models\TblExhibitorMessagesModel;
use CodeIgniter\RESTful\ResourceController;

class ExhibitorsController extends ResourceController
{
    protected string $modelName = TblExhibitorsModel::class;
    protected string $format = 'json';

    // GET /api/exhibitors
    public function index()
    {
        $exhibitors = $this->model->orderBy('company_name', 'ASC')->findAll();

        return $this->respond([
            'status' => 'success',
            'count' => count($exhibitors),
            'data' => $exhibitors
        ]);
    }

    // POST /api/exhibitors/{id}/message
    public function sendMessage($exhibitorId = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data['attendee_id']) || empty($data['message'])) {
            return $this->failValidationErrors('Missing attendee_id or message.');
        }

        $msgModel = new TblExhibitorMessagesModel();
        $msgModel->insert([
            'attendee_id'  => $data['attendee_id'],
            'exhibitor_id' => $exhibitorId,
            'message'      => $data['message'],
            'is_from_exhibitor' => 0
        ]);

        return $this->respondCreated(['status' => 'success', 'message' => 'Message sent successfully.']);
    }
}
