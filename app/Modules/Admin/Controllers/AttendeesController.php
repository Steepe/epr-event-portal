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
        $filters = $this->attendeeFilters();
        $attendees = $this->attendeesQuery($filters)
            ->orderBy('tbl_users.registered_2026_at', 'DESC')
            ->orderBy('tbl_attendees.registration_timestamp', 'DESC')
            ->findAll();

        return module_view('Admin', 'attendees/index', [
            'attendees' => $attendees,
            'conference' => $filters['conference'],
            'email' => $filters['email'],
            'country' => $filters['country'],
            'countries' => $this->countries(),
            'attendeeCount' => count($attendees),
        ]);
    }

    public function export()
    {
        return $this->exportAttendees($this->attendeeFilters());
    }

    public function export2026()
    {
        return $this->exportAttendees([
            'conference' => '2026',
            'email' => '',
            'country' => '',
        ]);
    }

    private function exportAttendees(array $filters)
    {
        $registrants = $this->attendeesQuery($filters, false)
            ->select('tbl_attendees.firstname, tbl_attendees.lastname, tbl_users.email, tbl_attendees.telephone, tbl_attendees.country, tbl_attendees.city, tbl_attendees.state, tbl_users.registered_2026_at')
            ->orderBy('tbl_users.registered_2026_at', 'DESC')
            ->orderBy('tbl_attendees.registration_timestamp', 'DESC')
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

        $filename = $filters['conference'] === '2026'
            ? 'emergence-2026-registrants'
            : 'attendees';
        if ($filters['email'] !== '' || $filters['country'] !== '') {
            $filename .= '-filtered';
        }

        return $this->response
            ->download($filename . '-' . date('Y-m-d') . '.csv', $csv)
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    private function attendeeFilters(): array
    {
        $email = trim((string) $this->request->getGet('email'));
        $country = trim((string) $this->request->getGet('country'));

        return [
            'conference' => $this->request->getGet('conference') === '2026' ? '2026' : null,
            'email' => mb_substr($email, 0, 254),
            'country' => mb_substr($country, 0, 100),
        ];
    }

    private function attendeesQuery(array $filters, bool $includePayment = true)
    {
        $query = (new AttendeesModel())
            ->select('tbl_attendees.*, tbl_users.email, tbl_users.registered_2026_at')
            ->join('tbl_users', 'tbl_users.id = tbl_attendees.attendee_id', 'left');

        if ($includePayment) {
            $query->select('tbl_attendee_payments.amount, tbl_attendee_payments.paymentdate')
                ->join('tbl_attendee_payments', 'tbl_attendee_payments.attendee_id = tbl_attendees.attendee_id', 'left');
        }

        if ($filters['conference'] === '2026') {
            $query->where('tbl_users.registered_2026_at IS NOT NULL', null, false);
        }

        if ($filters['email'] !== '') {
            $query->like('tbl_users.email', $filters['email']);
        }

        if ($filters['country'] !== '') {
            $query->where('tbl_attendees.country', $filters['country']);
        }

        return $query;
    }

    private function countries(): array
    {
        $rows = db_connect()->table('tbl_attendees')
            ->select('country')
            ->where('country IS NOT NULL', null, false)
            ->where("TRIM(country) <> ''", null, false)
            ->groupBy('country')
            ->orderBy('country', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($rows, 'country');
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
