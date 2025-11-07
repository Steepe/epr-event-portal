<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 06:35
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblSessionSpeakersModel;

class SessionSpeakersController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblSessionSpeakersModel::class;
    protected string $format    = 'json';

    // GET /api/speakers
    public function index()
    {
        $speakers = $this->model->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $speakers
        ]);
    }

    // GET /api/speakers/session/{sessionId}
    public function bySession($sessionId = null)
    {
        if (empty($sessionId)) {
            return $this->failValidationError('Session ID is required.');
        }

        $speakers = $this->model->where('sessions_id', $sessionId)->findAll();

        if (empty($speakers)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'No speakers found for this session.',
                'data' => []
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $speakers
        ]);
    }
}
