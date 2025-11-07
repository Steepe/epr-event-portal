<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 11:01
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Modules\Api\Models\TblTicketPricesModel;
use App\Modules\Api\Models\TblConferencesModel;

class TicketPricesController extends ResourceController
{
    use ResponseTrait;

    protected string $modelName = TblTicketPricesModel::class;
    protected string $format    = 'json';

    /**
     * GET /api/ticket-prices
     * Optional: filter by conference_id
     */
    public function index()
    {
        $conferenceId = $this->request->getGet('conference_id');

        if ($conferenceId) {
            $prices = $this->model
                ->where('conference_id', $conferenceId)
                ->orderBy('id', 'DESC')
                ->findAll();
        } else {
            $prices = $this->model->orderBy('id', 'DESC')->findAll();
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $prices,
        ]);
    }

    /**
     * GET /api/ticket-prices/{id}
     */
    public function show($id = null)
    {
        $ticket = $this->model->find($id);

        if (!$ticket) {
            return $this->failNotFound('Ticket price not found.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $ticket,
        ]);
    }

    /**
     * POST /api/ticket-prices
     * Create a new ticket price record
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (
            empty($data['conference_id']) ||
            !isset($data['amount_naira']) ||
            !isset($data['amount_shillings']) ||
            !isset($data['amount_rands']) ||
            !isset($data['amount_dollar'])
        ) {
            return $this->failValidationErrors('Missing required fields.');
        }

        $conferenceModel = new TblConferencesModel();
        $conference = $conferenceModel->find($data['conference_id']);
        if (!$conference) {
            return $this->failNotFound('Invalid conference ID.');
        }

        $ticketData = [
            'conference_id'   => $data['conference_id'],
            'amount_naira'    => $data['amount_naira'],
            'amount_shillings'=> $data['amount_shillings'],
            'amount_rands'    => $data['amount_rands'],
            'amount_dollar'   => $data['amount_dollar'],
            'status'          => $data['status'] ?? 'active',
            'effective_from'  => date('Y-m-d H:i:s'),
        ];

        if ($this->model->insert($ticketData)) {
            return $this->respondCreated([
                'status'  => 'success',
                'message' => 'Ticket price created successfully.',
                'data'    => $ticketData,
            ]);
        }

        return $this->failServerError('Failed to create ticket price.');
    }

    /**
     * PUT /api/ticket-prices/{id}
     */
    public function update($id = null)
    {
        if (!$id || !$this->model->find($id)) {
            return $this->failNotFound('Ticket price not found.');
        }

        $data = $this->request->getJSON(true);

        if ($this->model->update($id, $data)) {
            return $this->respond([
                'status'  => 'success',
                'message' => 'Ticket price updated successfully.',
            ]);
        }

        return $this->failServerError('Failed to update ticket price.');
    }

    /**
     * DELETE /api/ticket-prices/{id}
     */
    public function delete($id = null)
    {
        if (!$id || !$this->model->find($id)) {
            return $this->failNotFound('Ticket price not found.');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => 'success',
                'message' => 'Ticket price deleted successfully.',
            ]);
        }

        return $this->failServerError('Failed to delete ticket price.');
    }

    /**
     * GET /api/ticket-prices/{conference_id}/{country}
     * Fetch price based on user’s country
     */
    public function getPrice($conference_id = null, $country = null): \CodeIgniter\HTTP\ResponseInterface
    {
        $ticket = $this->model->where('conference_id', $conference_id)->first();

        if (!$ticket) {
            return $this->failNotFound('Ticket prices not found.');
        }

        switch (strtolower($country)) {
            case 'nigeria':
                $price = $ticket['amount_naira'];
                $currency = 'NGN';
                break;
            case 'kenya':
                $price = $ticket['amount_shillings'];
                $currency = 'KES';
                break;
            case 'south africa':
                $price = $ticket['amount_rands'];
                $currency = 'ZAR';
                break;
            default:
                $price = $ticket['amount_dollar'];
                $currency = 'USD';
        }

        return $this->respond([
            'status'   => 'success',
            'price'    => $price,
            'currency' => $currency,
        ]);
    }

}
