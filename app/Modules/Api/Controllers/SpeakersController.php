<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 20:44
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Modules\Api\Models\TblSpeakersModel;
use App\Modules\Api\Models\TblConferenceSessionsModel;

class SpeakersController extends ResourceController
{
    protected string $modelName = TblSpeakersModel::class;
    protected string $format    = 'json';

    // GET /api/speakers
    public function index()
    {
        try {
            $speakersModel = new TblSpeakersModel();
            $sessionModel  = new TblConferenceSessionsModel();

            // Fetch all speakers
            $speakers = $speakersModel->findAll();

            // Attach sessions if any
            foreach ($speakers as &$speaker) {
                $speaker['sessions'] = $sessionModel
                    ->select('sessions_name')
                    ->where('sessions_id', $speaker['sessions_id'])
                    ->findAll();
            }

            return $this->respond([
                'status'   => 'success',
                'data'     => $speakers,
                'count'    => count($speakers)
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Failed to load speakers: ' . $e->getMessage());
            return $this->failServerError('Unable to load speakers.');
        }
    }
}
