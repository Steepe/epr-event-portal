<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 05:26
 */

namespace App\Modules\Api\Controllers;

use App\Modules\Api\Models\TblConferenceSessionsModel;
use App\Modules\Api\Models\TblConferenceSponsorsModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ConferenceSessionsController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblConferenceSessionsModel::class;
    protected string $format = 'json';

    // GET /api/sessions/{conference_id}
    public function index($conference_id = null)
    {
        if (!$conference_id) {
            return $this->failValidationErrors('Conference ID required.');
        }

        $sessionModel = new TblConferenceSessionsModel();
        $sponsorModel = new TblConferenceSponsorsModel();

        $sessions = $sessionModel->getSessionsWithRelations($conference_id);
        $conferenceSponsors = $sponsorModel->getSponsorsByConference($conference_id);

        if (!$sessions) {
            return $this->failNotFound('No sessions found for this conference.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'sessions' => $sessions,
                'conference_sponsors' => $conferenceSponsors
            ]
        ]);
    }

    // GET /api/sessions/view/{id}
    public function show($id = null)
    {
        $model = new TblConferenceSessionsModel();
        $session = $model->find($id);

        if (!$session) {
            return $this->failNotFound('Session not found.');
        }

        // Optionally add speakers & sponsors for this session
        $sessionDetails = $model->getSessionsWithRelations($session['conference_id']);
        $sessionDetails = array_values(array_filter($sessionDetails, fn($s) => $s['sessions_id'] == $id))[0] ?? [];

        return $this->respond([
            'status' => 'success',
            'data'   => $sessionDetails
        ]);
    }
}
