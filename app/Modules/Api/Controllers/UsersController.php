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

        // Load user + attendee profile
        $user = $this->model
            ->select('tbl_users.id, tbl_users.email, tbl_users.role,
                  tbl_attendees.firstname, tbl_attendees.lastname,
                  tbl_attendees.country, tbl_attendees.profile_picture,
                  tbl_attendees.company, tbl_attendees.position')
            ->join('tbl_attendees', 'tbl_attendees.attendee_id = tbl_users.id', 'left')
            ->where('tbl_users.id', $id)
            ->first();

        if (!$user) {
            return $this->failNotFound('User not found.');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $user
        ]);
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
