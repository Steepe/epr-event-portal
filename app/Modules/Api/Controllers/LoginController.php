<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 18:10
 */

namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Modules\Api\Models\TblUsersModel;
use App\Modules\Api\Models\TblAttendeesModel;

class LoginController extends ResourceController
{
    protected string $format = 'json';

    // Must be STRING, not typed.
    protected string $modelName = 'App\Modules\Api\Models\TblAttendeesModel';

    protected TblUsersModel $users;
    protected TblAttendeesModel $attendees;

    public function __construct()
    {
        $this->users     = new TblUsersModel();
        $this->attendees = new TblAttendeesModel();
    }

    public function login()
    {
        // Validate API key
        $apiKey = $this->request->getHeaderLine('X-API-KEY');
        if ($apiKey !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API key.');
        }

        // Read JSON
        $data = $this->request->getJSON(true);
        if (empty($data['email']) || empty($data['password'])) {
            return $this->failValidationErrors('Email and password are required.');
        }

        // Fetch user
        $user = $this->users->where('email', $data['email'])->first();
        if (!$user) {
            return $this->failNotFound('User not found.');
        }

        // Normalize entity/array
        if (is_array($user)) {
            $userArray = $user;
        } elseif (is_object($user) && method_exists($user, 'toArray')) {
            $userArray = $user->toArray();
        } else {
            $userArray = (array) $user;
        }

        // Validate password
        if (!password_verify($data['password'], $userArray['password'])) {
            return $this->fail('Invalid credentials.', 401);
        }

        // Fetch attendee
        $attendee = $this->attendees->where('attendee_id', $userArray['id'])->first();
        if (!$attendee) {
            return $this->fail('Attendee profile missing.', 500);
        }

        // Normalize attendee
        if (is_array($attendee)) {
            $attendeeArray = $attendee;
        } elseif (is_object($attendee) && method_exists($attendee, 'toArray')) {
            $attendeeArray = $attendee->toArray();
        } else {
            $attendeeArray = (array) $attendee;
        }

        // Store session data
        session()->set([
            'logged_in'   => true,
            'user_id'     => $userArray['id'],
            'uuid'        => $userArray['uuid'],
            'email'       => $userArray['email'],
            'role'        => $userArray['role'],
            'firstname'   => $attendeeArray['firstname'],
            'lastname'    => $attendeeArray['lastname'],
            'attendee_id' => $attendeeArray['id'],     // PK of tbl_attendees
            'reg_country' => $attendeeArray['country'],
            'profile_pic' => $attendeeArray['profile_picture'],
        ]);

        // Update last login
        $this->users->update($userArray['id'], [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login successful',
            'user' => [
                'id'    => $userArray['id'],
                'uuid'  => $userArray['uuid'],
                'email' => $userArray['email'],
                'role'  => $userArray['role'],
            ],
            'attendee' => [
                'attendee_id'     => $attendeeArray['id'],
                'firstname'       => $attendeeArray['firstname'],
                'lastname'        => $attendeeArray['lastname'],
                'country'         => $attendeeArray['country'],
                'city'            => $attendeeArray['city'],
                'state'           => $attendeeArray['state'],
                'profile_picture' => $attendeeArray['profile_picture'],
            ]
        ]);
    }
}
