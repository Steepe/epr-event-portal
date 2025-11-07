<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 11:54
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Modules\Api\Models\TblConferencesModel;

class ConferenceController extends ResourceController
{
    protected string $modelName = TblConferencesModel::class;
    protected string $format    = 'json';

    // GET /api/conferences
    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'data'   => $this->model->findAll()
        ]);
    }

    // POST /api/conferences
    public function create()
    {
        $data = $this->request->getJSON(true);
        if (!$data || empty($data['name']) || empty($data['slug'])) {
            return $this->failValidationErrors('Conference name and slug required.');
        }

        $id = $this->model->insert($data);
        return $this->respondCreated([
            'status' => 'success',
            'id'     => $id
        ]);
    }

    // GET /api/conferences/{slug}
    public function show($slug = null)
    {
        $conference = $this->model->where('slug', $slug)->first();
        if (!$conference) {
            return $this->failNotFound('Conference not found');
        }
        return $this->respond(['status' => 'success', 'data' => $conference]);
    }

    // GET /api/conferences/live
    public function live()
    {
        $model = new TblConferencesModel();

        // Find the one that is live
        $conference = $model->where('is_live', (int)1)->first();
        log_message('debug', 'Live conference query: ' . $model->getLastQuery());


        if (!$conference) {
            return $this->failNotFound('Conference not found');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $conference
        ]);
    }

    // GET /api/conferences/past
    public function past()
    {
        $data = $this->model->where('is_live', 0)->findAll();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

}
