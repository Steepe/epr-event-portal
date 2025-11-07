<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 07:56
 */

namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Modules\Api\Models\TblUsersModel;
use App\Modules\Api\Models\TblAttendeeSessionsModel;

class UsersController extends ResourceController
{
    protected string $modelName = TblUsersModel::class;
    protected string $format    = 'json';

    public function show($id = null)
    {
        $key = $this->request->getHeaderLine('X-API-KEY');
        if ($key !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API key.');
        }

        $user = $this->model->find($id);
        if (!$user) return $this->failNotFound('User not found.');

        $data = [
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'country' => $user->country,
            'role' => $user->role,
            'plan' => $user->plan ?? 1,
            'profile_picture' => $user->profile_picture ?? '',
        ];

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    public function attendeeSessions($attendeeId = null)
    {
        $key = $this->request->getHeaderLine('X-API-KEY');
        if ($key !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API key.');
        }

        $sessionModel = new TblAttendeeSessionsModel();
        $sessions = $sessionModel
            ->select('conference_session_id')
            ->where('attendee_id', $attendeeId)
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => array_column($sessions, 'conference_session_id'),
        ]);
    }
}
