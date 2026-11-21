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
use App\Libraries\AttendeePasswordReset;
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
            'firstName' => $filters['first_name'],
            'lastName' => $filters['last_name'],
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
            'first_name' => '',
            'last_name' => '',
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
        if ($filters['first_name'] !== '' || $filters['last_name'] !== '' || $filters['email'] !== '' || $filters['country'] !== '') {
            $filename .= '-filtered';
        }

        return $this->response
            ->download($filename . '-' . date('Y-m-d') . '.csv', $csv)
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    private function attendeeFilters(): array
    {
        $firstName = trim((string) $this->request->getGet('first_name'));
        $lastName = trim((string) $this->request->getGet('last_name'));
        $email = trim((string) $this->request->getGet('email'));
        $country = trim((string) $this->request->getGet('country'));

        return [
            'conference' => $this->request->getGet('conference') === '2026' ? '2026' : null,
            'first_name' => mb_substr($firstName, 0, 100),
            'last_name' => mb_substr($lastName, 0, 100),
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

        if ($filters['first_name'] !== '') {
            $query->like('tbl_attendees.firstname', $filters['first_name']);
        }

        if ($filters['last_name'] !== '') {
            $query->like('tbl_attendees.lastname', $filters['last_name']);
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

    /**
     * Invalidates an attendee's existing password and emails the only active,
     * one-time link they can use to create their replacement password.
     */
    public function resetPassword(int $id)
    {
        $db = db_connect();
        $attendee = $db->table('tbl_attendees a')
            ->select('a.id, a.attendee_id, a.firstname, a.lastname, u.email')
            ->join('tbl_users u', 'u.id = a.attendee_id', 'inner')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();

        if (! $attendee || empty($attendee['email']) || empty($attendee['attendee_id'])) {
            return redirect()->to(site_url('admin/attendees'))
                ->with('error', 'This attendee does not have a portal account with a valid email address.');
        }

        try {
            $sent = (new AttendeePasswordReset($db))->sendAdminResetLink((string) $attendee['email']);
            if (! $sent) {
                throw new \RuntimeException('The attendee account could not be found.');
            }

            // Do this only after the email service has accepted the message.
            // The random value is never disclosed; the one-time email link is
            // the only way to create the next usable password.
            $db->table('tbl_users')->where('id', (int) $attendee['attendee_id'])->update([
                'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            if ($db->affectedRows() !== 1) {
                throw new \RuntimeException('The reset email was sent, but the previous password could not be invalidated.');
            }
            (new \App\Services\AttendeeRememberMe())->revokeAllForUser((int) $attendee['attendee_id']);

            return redirect()->to(site_url('admin/attendees'))
                ->with('success', 'Password reset email sent to ' . $attendee['email'] . '. Their old password no longer works; they must create a new password from the email before signing in.');
        } catch (\Throwable $error) {
            log_message('error', 'Admin attendee password reset failed ({type}).', ['type' => get_class($error)]);

            return redirect()->to(site_url('admin/attendees'))
                ->with('error', 'The password reset email could not be completed. Please try again.');
        }
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
