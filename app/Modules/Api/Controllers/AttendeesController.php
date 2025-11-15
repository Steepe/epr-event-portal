<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 00:16
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblAttendeesModel;
use App\Modules\Api\Models\TblUsersModel;

class AttendeesController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblAttendeesModel::class;
    protected string $format    = 'json';

    /**
     * GET /api/attendees
     */
    public function index()
    {
        try {
            $name = $this->request->getGet('attendee_name');
            $sort = $this->request->getGet('sort');

            $builder = $this->model
                ->select("
        tbl_attendees.id,
        tbl_attendees.attendee_id,
        tbl_attendees.firstname,
        tbl_attendees.lastname,
        tbl_attendees.country,
        tbl_attendees.state,
        tbl_attendees.city,
        tbl_attendees.profile_picture,
        tbl_users.email
    ")
                ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id');

            if ($name) {
                $builder->groupStart()
                    ->like('tbl_attendees.firstname', $name)
                    ->orLike('tbl_attendees.lastname', $name)
                    ->groupEnd();
            }

            if ($sort) {
                $builder->like('tbl_attendees.firstname', $sort, 'after');
            }

            $attendees = $builder
                ->orderBy('tbl_attendees.firstname', 'ASC')
                ->findAll();

            // Generate A–Z letters
            $alphabetCount = [];
            foreach (range('A', 'Z') as $letter) {
                $alphabetCount[] = ['firstCharacter' => $letter];
            }

            return $this->respond([
                'status'  => 200,
                'message' => 'Attendees retrieved successfully.',
                'data'    => [
                    'attendees'      => $attendees,
                    'alphabet_count' => $alphabetCount,
                ],
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[API:AttendeesController] '.$e->getMessage());
            return $this->failServerError('Unable to fetch attendees.');
        }
    }

    /**
     * GET /api/attendees/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->failValidationError('Missing attendee ID.');
        }

        $attendee = $this->model
            ->select("
                tbl_attendees.id,
                tbl_attendees.attendee_id,
                tbl_attendees.firstname,
                tbl_attendees.lastname,
                tbl_attendees.country,
                tbl_attendees.state,
                tbl_attendees.city,
                tbl_users.email
            ")
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id')
            ->find($id);

        if (!$attendee) {
            return $this->failNotFound('Attendee not found.');
        }

        return $this->respond([
            'status' => 200,
            'data'   => $attendee,
        ]);
    }

    /**
     * GET /api/attendees/all (requires X-API-KEY)
     */
    public function all()
    {
        $key = $this->request->getHeaderLine('X-API-KEY');
        if ($key !== env('api.securityKey')) {
            return $this->failUnauthorized('Invalid API key');
        }

        $rows = $this->model
            ->select("
                tbl_attendees.id,
                tbl_attendees.firstname,
                tbl_attendees.lastname
            ")
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $rows,
        ]);
    }
}
