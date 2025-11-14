<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 14/11/2025
 * Time: 05:00
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    /**
     * Show Attendee Profile Page
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        $attendeeId = $session->get('attendee_id');

        // Load attendee from DB
        $profile = $this->db->table('tbl_attendees')
            ->where('attendee_id', $attendeeId)
            ->get()
            ->getRowArray();

        if (!$profile) {
            return redirect()->to(base_url('attendees/home'))
                ->with('error', 'Profile not found.');
        }

        $data = [
            'profile' => $profile,
        ];

        return module_view('Web', 'profile', $data);
    }

    /**
     * Handle profile update + picture upload
     */
    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $attendeeId = $this->request->getPost('attendee_id');

        // Validate basic fields
        $validation = \Config\Services::validation();
        $validation->setRules([
            'firstname' => 'required|min_length[2]',
            'lastname' => 'required|min_length[2]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('error', implode(', ', $validation->getErrors()));
        }

        // Prepare update data
        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'telephone' => $this->request->getPost('telephone'),
            'country' => $this->request->getPost('country'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'company' => $this->request->getPost('company'),
            'position' => $this->request->getPost('position'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // ========================
        // HANDLE PROFILE PICTURE
        // ========================
        $file = $this->request->getFile('profile_picture');

        if ($file && $file->isValid() && !$file->hasMoved()) {

            // Validate image
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
                return redirect()->back()->with('error', 'Invalid image format.');
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/attendee_pictures/', $newName);

            $data['profile_picture'] = $newName;

            // Update session picture as well
            $session->set('profile_picture', $newName);
        }

        // ========================
        // UPDATE DATABASE RECORD
        // ========================
        $this->db->table('tbl_attendees')
            ->where('attendee_id', $attendeeId)
            ->update($data);

        // Update session name
        $session->set('firstname', $data['firstname']);
        $session->set('lastname', $data['lastname']);

        return redirect()->to(base_url('attendees/profile'))
            ->with('success', 'Profile updated successfully.');
    }

    public function uploadPhoto(): \CodeIgniter\HTTP\ResponseInterface
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not logged in'
            ]);
        }

        $file = $this->request->getFile('profile_picture');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid file upload.'
            ]);
        }

        // Validate mime type
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowed)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unsupported file type.'
            ]);
        }

        // Move file
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/attendee_pictures/', $newName);

        // Update DB
        $this->db->table('tbl_attendees')
            ->where('attendee_id', $session->get('attendee_id'))
            ->update(['profile_picture' => $newName]);

        // Update session
        $session->set('profile_picture', $newName);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Photo updated.',
            'url'     => base_url('uploads/attendee_pictures/' . $newName)
        ]);
    }

}
