<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:47
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\AttendeesModel;

class AttendeesController extends BaseController
{
    protected AttendeesModel $attendeesModel;

    public function __construct()
    {
        $this->attendeesModel = new AttendeesModel();
    }

    public function index()
    {
        $attendees = $this->attendeesModel
            ->select('tbl_attendees.*, tbl_users.email, tbl_attendee_payments.amount, tbl_attendee_payments.paymentdate')
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id', 'left')
            ->join('tbl_attendee_payments', 'tbl_attendee_payments.attendee_id = tbl_attendees.attendee_id', 'left')
            ->orderBy('tbl_attendees.registration_timestamp', 'DESC')
            ->findAll();

        return module_view('Admin', 'attendees/index', ['attendees' => $attendees]);
    }

    public function update($id)
    {
        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname'  => $this->request->getPost('lastname'),
            'plan'      => $this->request->getPost('plan'),
        ];

        if ($this->attendeesModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function delete($id)
    {
        if ($this->attendeesModel->delete($id)) {
            return $this->response->setJSON(['status' => 'deleted']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function view($id = null)
    {
        $attendee = $this->attendeesModel
            ->select('tbl_attendees.*, tbl_users.email, tbl_attendee_payments.amount, tbl_attendee_payments.paymentdate')
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id', 'left')
            ->join('tbl_attendee_payments', 'tbl_attendee_payments.attendee_id = tbl_attendees.attendee_id', 'left')
            ->where('tbl_attendees.id', $id)
            ->first();

        if (!$attendee) {
            session()->setFlashdata('error', 'Attendee not found.');
            return redirect()->to(site_url('admin/attendees'));
        }

        return module_view('Admin', 'attendees/view', ['attendee' => $attendee]);
    }

    public function edit($id = null)
    {
        $attendee = $this->attendeesModel->find($id);

        if (!$attendee) {
            session()->setFlashdata('error', 'Attendee not found.');
            return redirect()->to(site_url('admin/attendees'));
        }

        return module_view('Admin', 'attendees/edit', ['attendee' => $attendee]);
    }

}
