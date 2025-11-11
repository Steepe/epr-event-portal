<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 03:03
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\ConferencesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ConferencesController extends BaseController
{
    protected ConferencesModel $conferenceModel;

    public function __construct()
    {
        $this->conferenceModel = new ConferencesModel();
    }

    public function index()
    {
        $conferences = $this->conferenceModel
            ->orderBy('start_date', 'DESC')
            ->findAll();

        echo module_view('Admin', 'conferences/index', [
            'conferences' => $conferences,
            'pageTitle' => 'Manage Conferences',
        ]);
    }

    public function create()
    {
        echo module_view('Admin', 'conferences/create', [
            'pageTitle' => 'Create Conference',
        ]);
    }

    public function edit($id = null)
    {
        $conference = $this->conferenceModel->find($id);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        echo module_view('Admin', 'conferences/edit', [
            'conference' => $conference,
            'pageTitle' => 'Edit Conference',
        ]);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $title = trim((string)$this->request->getPost('title'));
        if ($title === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Conference title is required.');
        }

        $data = [
            'title'        => $title, // still mapped to DB field `name`
            'slug'        => url_title($title, '-', true) ?: uniqid('conf-'),
            'description' => $this->request->getPost('description'),
            'year'        => $this->request->getPost('year'),
            'is_paid'     => $this->request->getPost('is_paid') ? 1 : 0,
            'status'      => $this->request->getPost('status'),
            'days'        => $this->request->getPost('days'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
        ];

        // ✅ Enforce one-live rule
        if ($data['status'] === 'live') {
            $liveExists = $this->conferenceModel->where('status', 'live')->countAllResults();
            if ($liveExists > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Another conference is already marked as live.');
            }
        }

// ✅ Handle icon upload
        $iconFile = $this->request->getFile('icon');
        if ($iconFile && $iconFile->isValid() && !$iconFile->hasMoved()) {
            $newName = $iconFile->getRandomName();
            $iconFile->move(FCPATH . 'uploads/conferences', $newName);
            $data['icon'] = $newName; // 👈 only filename stored in DB
        }

        $this->conferenceModel->insert($data);
        return redirect()
            ->to(site_url('admin/conferences'))
            ->with('success', 'Conference created successfully.');
    }

    public function update($id = null): \CodeIgniter\HTTP\RedirectResponse
    {
        $conference = $this->conferenceModel->find($id);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        $data = [
            'title'        => trim($this->request->getPost('title')),
            'description' => $this->request->getPost('description'),
            'year'        => $this->request->getPost('year'),
            'is_paid'     => $this->request->getPost('is_paid') ? 1 : 0,
            'status'      => $this->request->getPost('status'),
            'days'        => $this->request->getPost('days'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
        ];

        // ✅ Enforce one live conference rule (excluding current one)
        if ($data['status'] === 'live') {
            $liveExists = $this->conferenceModel
                ->where('status', 'live')
                ->where('conference_id !=', $id)
                ->countAllResults();

            if ($liveExists > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Another conference is already marked as live.');
            }
        }

        // ✅ Handle icon upload (replace old one if uploaded)
        $iconFile = $this->request->getFile('icon');
        if ($iconFile && $iconFile->isValid() && !$iconFile->hasMoved()) {
            $newName = $iconFile->getRandomName();
            $iconFile->move(FCPATH . 'uploads/conferences', $newName);
            $data['icon'] = 'uploads/conferences/' . $newName;

            // Delete old icon if it exists
            if (!empty($conference['icon']) && file_exists(FCPATH . $conference['icon'])) {
                @unlink(FCPATH . $conference['icon']);
            }
        }

        $this->conferenceModel->update($id, $data);
        return redirect()
            ->to(site_url('admin/conferences'))
            ->with('success', 'Conference updated successfully.');
    }

    public function delete($id = null): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->conferenceModel->delete($id);
        return redirect()->to(site_url('admin/conferences'))->with('success', 'Conference deleted.');
    }
}
