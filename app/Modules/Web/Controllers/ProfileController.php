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

        // IMPORTANT: Your new table uses `id` as PK, and `attendee_id` is FK to tbl_users.
        $rowId = $this->request->getPost('id'); // change from attendee_id

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'firstname' => 'required|min_length[2]',
            'lastname'  => 'required|min_length[2]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('error', implode(', ', $validation->getErrors()));
        }

        // Build update array from new DB structure
        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname'  => $this->request->getPost('lastname'),
            'telephone' => $this->request->getPost('telephone'),
            'country'   => $this->request->getPost('country'),
            'city'      => $this->request->getPost('city'),
            'state'     => $this->request->getPost('state'),
            'company'   => $this->request->getPost('company'),
            'position'  => $this->request->getPost('position'),

            // NEW SOCIALS
            'facebook'  => $this->request->getPost('facebook'),
            'twitter'   => $this->request->getPost('twitter'),
            'instagram' => $this->request->getPost('instagram'),
            'linkedin'  => $this->request->getPost('linkedin'),

            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Picture upload removed – now handled by uploadPhoto()

        // Update row using the actual PK "id"
        $this->db->table('tbl_attendees')
            ->where('id', $rowId)
            ->update($data);

        // Sync session values
        $session->set('firstname', $data['firstname']);
        $session->set('lastname',  $data['lastname']);

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
