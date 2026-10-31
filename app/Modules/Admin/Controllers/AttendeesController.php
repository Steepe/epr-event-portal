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
        $conference = $this->request->getGet('conference') === '2026' ? '2026' : null;

        $attendeesQuery = $this->attendeesModel
            ->select('tbl_attendees.*, tbl_users.email, tbl_users.registered_2026_at, tbl_attendee_payments.amount, tbl_attendee_payments.paymentdate')
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id', 'left')
            ->join('tbl_attendee_payments', 'tbl_attendee_payments.attendee_id = tbl_attendees.attendee_id', 'left');

        if ($conference === '2026') {
            $attendeesQuery->where('tbl_users.registered_2026_at IS NOT NULL', null, false);
        }

        $attendees = $attendeesQuery
            ->orderBy('tbl_users.registered_2026_at', 'DESC')
            ->orderBy('tbl_attendees.registration_timestamp', 'DESC')
            ->findAll();

        return module_view('Admin', 'attendees/index', [
            'attendees' => $attendees,
            'conference' => $conference,
        ]);
    }

    public function export2026()
    {
        $registrants = $this->attendeesModel
            ->select('tbl_attendees.firstname, tbl_attendees.lastname, COALESCE(tbl_users.email, tbl_attendees.useremail) AS email, tbl_attendees.telephone, tbl_attendees.country, tbl_attendees.city, tbl_attendees.state, tbl_users.registered_2026_at', false)
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id', 'left')
            ->where('tbl_users.registered_2026_at IS NOT NULL', null, false)
            ->orderBy('tbl_users.registered_2026_at', 'DESC')
            ->findAll();

        $stream = fopen('php://temp', 'r+');
        $safeCsvValue = static function ($value): string {
            $value = (string) $value;

            return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
        };

        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, [
            'First Name',
            'Last Name',
            'Email',
            'Telephone',
            'Country',
            'City',
            'State',
            'Registered for Emergence 2026',
        ]);

        foreach ($registrants as $registrant) {
            fputcsv($stream, [
                $safeCsvValue($registrant['firstname'] ?? ''),
                $safeCsvValue($registrant['lastname'] ?? ''),
                $safeCsvValue($registrant['email'] ?? ''),
                $safeCsvValue($registrant['telephone'] ?? ''),
                $safeCsvValue($registrant['country'] ?? ''),
                $safeCsvValue($registrant['city'] ?? ''),
                $safeCsvValue($registrant['state'] ?? ''),
                $safeCsvValue($registrant['registered_2026_at'] ?? ''),
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->download('emergence-2026-registrants-' . date('Y-m-d') . '.csv', $csv)
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8');
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
