<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 16:11
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    /**
     * Display the logged-in attendee's profile
     */
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $attendeeId = session()->get('user_id');

        $profile = $this->db->table('tbl_attendees')
            ->where('attendee_id', $attendeeId)
            ->get()
            ->getRowArray();

        if (! $profile) {
            return redirect()->to(site_url('mobile/lobby'))
                ->with('error', 'Profile not found.');
        }

        return module_view('MobileApp', 'profile', [
            'profile' => $profile
        ]);
    }

    /**
     * Save profile data
     */
    public function update()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $attendeeId = session()->get('user_id');

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname'  => $this->request->getPost('lastname'),
            'telephone' => $this->request->getPost('telephone'),
            'country'   => $this->request->getPost('country'),
            'city'      => $this->request->getPost('city'),
            'state'     => $this->request->getPost('state'),
            'company'   => $this->request->getPost('company'),
            'position'  => $this->request->getPost('position'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ];

        // Handle profile picture
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/attendee_pictures/', $newName);
            $data['profile_picture'] = $newName;
            session()->set('profile_picture', $newName);
        }

        // Update database
        $this->db->table('tbl_attendees')
            ->where('attendee_id', $attendeeId)
            ->update($data);

        // Update session
        session()->set([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
        ]);

        return redirect()->to(site_url('mobile/profile'))
            ->with('success', 'Profile updated successfully.');
    }


    /**
     * AJAX Photo Upload (Mobile Camera)
     */
    public function uploadPhoto()
    {
        if (! session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not authenticated'
            ]);
        }

        $file = $this->request->getFile('profile_picture');
        if (! $file || ! $file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid file'
            ]);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (! in_array($file->getMimeType(), $allowed)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unsupported image format'
            ]);
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/attendee_pictures/', $newName);

        $this->db->table('tbl_attendees')
            ->where('attendee_id', session()->get('user_id'))
            ->update(['profile_picture' => $newName]);

        session()->set('profile_picture', $newName);

        return $this->response->setJSON([
            'status' => 'success',
            'url'    => base_url('uploads/attendee_pictures/'.$newName),
            'message'=> 'Photo updated successfully'
        ]);
    }
}
