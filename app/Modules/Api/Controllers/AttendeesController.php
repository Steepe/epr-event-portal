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

class AttendeesController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblAttendeesModel::class;
    protected string $format    = 'json';

    /**
     * GET /api/attendees
     * Query params: ?attendee_name=John  or  ?sort=A
     */
    public function index()
    {
        try {
            $name  = $this->request->getGet('attendee_name');
            $sort  = $this->request->getGet('sort');

            $builder = $this->model->select(
                'id, attendee_id, firstname, lastname, useremail, country, state, city'
            );

            if ($name) {
                $builder->groupStart()
                    ->like('firstname', $name)
                    ->orLike('lastname', $name)
                    ->groupEnd();
            }

            if ($sort) {
                $builder->like('firstname', $sort, 'after');
            }

            $attendees = $builder->orderBy('firstname', 'ASC')->findAll();

            // Build A–Z sorting links
            $alphabetCount = [];
            foreach (range('A', 'Z') as $letter) {
                $alphabetCount[] = ['firstCharacter' => $letter];
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Attendees retrieved successfully.',
                'data' => [
                    'attendees'       => $attendees,
                    'alphabet_count'  => $alphabetCount,
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
            ->select('id, attendee_id, firstname, lastname, useremail, country, state, city')
            ->find($id);

        if (!$attendee) {
            return $this->failNotFound('Attendee not found.');
        }

        return $this->respond([
            'status' => 200,
            'data'   => $attendee,
        ]);
    }
}
