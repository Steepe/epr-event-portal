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
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblSpeakersModel;

class SpeakersController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblSpeakersModel::class;
    protected string $format = 'json';

    /**
     * GET /api/speakers
     * Returns all speakers directly from tbl_speakers
     */
    public function index()
    {
        try {
            $speakers = $this->model->findAll();

            return $this->respond([
                'status' => 'success',
                'data'   => $speakers,
                'count'  => count($speakers)
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[SpeakersController] Failed to fetch speakers: ' . $e->getMessage());
            return $this->failServerError('Unable to load speakers.');
        }
    }

    /**
     * Optional: GET /api/speakers/(:num)
     * Returns a single speaker by ID
     */
    public function show($id = null)
    {
        try {
            $speaker = $this->model->find($id);
            if (!$speaker) {
                return $this->failNotFound('Speaker not found.');
            }

            return $this->respond([
                'status' => 'success',
                'data'   => $speaker
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[SpeakersController] Error fetching speaker ID '.$id.': '.$e->getMessage());
            return $this->failServerError('Unable to load speaker.');
        }
    }

    public function offers($speakerId)
    {
        $apiKey = $this->request->getHeaderLine('X-API-KEY');
        if ($apiKey !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API Key.');
        }

        $db = db_connect();

        $offers = $db->table('tbl_speaker_offers')
            ->where('speaker_id', $speakerId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data'   => $offers
        ]);
    }

}
