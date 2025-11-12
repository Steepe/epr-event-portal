<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 17:06
 */


namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblUsersModel;
use App\Modules\Api\Models\TblAttendeesModel;
use Ramsey\Uuid\Uuid;

class RegistrationController extends BaseController
{
    use ResponseTrait;

    protected TblUsersModel $users;
    protected TblAttendeesModel $attendees;

    public function __construct()
    {
        $this->users     = new TblUsersModel();
        $this->attendees = new TblAttendeesModel();
    }

    public function register()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email']) || empty($data['password'])) {
            return $this->failValidationErrors('Missing required fields.');
        }

        if ($this->users->where('email', $data['email'])->first()) {
            return $this->failResourceExists('Email already registered.');
        }

        $uuid           = Uuid::uuid4()->toString();
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $ipAddress      = $this->request->getIPAddress();

        $userData = [
            'uuid'        => $uuid,
            'email'       => $data['email'],
            'password'    => $hashedPassword,
            'role'        => 'attendee',
            'is_verified' => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $userId = $this->users->insert($userData);
        if (!$userId) {
            return $this->failServerError('Failed to create user.');
        }

        $attendeeData = [
            'attendee_id'            => $userId,
            'firstname'              => $data['firstname'],
            'lastname'               => $data['lastname'],
            'telephone'              => $data['telephone'] ?? '',
            'country'                => $data['country'] ?? '',
            'city'                   => $data['city'] ?? '',
            'state'                  => $data['state'] ?? '',
            'ipaddress'              => $ipAddress,
            'uid'                    => substr(md5(uniqid('', true)), 0, 11),
            'is_verified'            => 1,
            'registration_timestamp' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->attendees->insert($attendeeData);
        } catch (\Throwable $e) {
            log_message('warning', 'Attendee insert failed: ' . $e->getMessage());
        }

        $emailService = \Config\Services::email();
        $emailService->setFrom(env('email.fromEmail'), 'EPRGlobal Events');
        $emailService->setTo($data['email']);
        $emailService->setSubject('Welcome to EPRGlobal Event Portal!');
        $emailService->setMessage(module_view('Api', 'welcome_email', [
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
        ]));

        if (!$emailService->send()) {
            log_message('error', 'Registration email failed: ' . print_r($emailService->printDebugger(['headers']), true));
        }

        return $this->respondCreated([
            'status'       => 'success',
            'message'      => 'Registration successful',
            'notification' => 'A welcome email has been sent to your inbox. Please check your spam folder if not found.',
            'user'         => [
                'id'    => $userId,
                'uuid'  => $uuid,
                'email' => $data['email'],
            ],
        ]);
    }
}
