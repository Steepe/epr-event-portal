<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:48
 */


namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\ExhibitorsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ExhibitorsController extends BaseController
{
    protected ExhibitorsModel $exhibitorsModel;

    public function __construct()
    {
        $this->exhibitorsModel = new ExhibitorsModel();
    }

    public function index()
    {
        $exhibitors = $this->exhibitorsModel->orderBy('company_name', 'ASC')->findAll();

        echo module_view('Admin', 'exhibitors/index', [
            'exhibitors' => $exhibitors,
            'pageTitle'  => 'Exhibitors'
        ]);
    }

    public function create()
    {
        echo module_view('Admin', 'exhibitors/create', [
            'pageTitle' => 'Add Exhibitor'
        ]);
    }

    public function store()
    {
        helper(['form', 'url']);

        $logo = $this->request->getFile('logo');
        $logoFilename = null;

        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/exhibitors/', $newName);
            $logoFilename = $newName; // ✅ Store filename only
        }

        $data = [
            'company_name'     => $this->request->getPost('company_name'),
            'contact_person'   => $this->request->getPost('contact_person'),
            'email'            => $this->request->getPost('email'),
            'telephone'        => $this->request->getPost('telephone'),
            'website'          => $this->request->getPost('website'),
            'tagline'          => $this->request->getPost('tagline'),
            'vimeo_id'         => $this->request->getPost('vimeo_id'),
            'profile_summary'  => $this->request->getPost('profile_summary'),
            'has_promotion'    => $this->request->getPost('has_promotion') ? 1 : 0,
            'promotion_text'   => $this->request->getPost('promotion_text'),
            'logo'             => $logoFilename,
        ];

        $this->exhibitorsModel->insert($data);

        return redirect()->to(site_url('admin/exhibitors'))
            ->with('success', 'Exhibitor added successfully.');
    }

    public function edit($id = null)
    {
        // Fetch exhibitor record
        $exhibitor = $this->exhibitorsModel->find($id);

        if (! $exhibitor) {
            throw new PageNotFoundException('Exhibitor not found.');
        }

        // Render the edit view
        echo module_view('Admin', 'exhibitors/edit', [
            'exhibitor'  => $exhibitor,
            'pageTitle'  => 'Edit Exhibitor - ' . esc($exhibitor['company_name']),
        ]);
    }

    public function update($id = null)
    {
        helper(['form', 'url']);

        $exhibitor = $this->exhibitorsModel->find($id);
        if (! $exhibitor) {
            throw new PageNotFoundException('Exhibitor not found.');
        }

        $logo = $this->request->getFile('logo');
        $logoFilename = $exhibitor['logo']; // Keep old logo by default

        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/exhibitors/', $newName);
            $logoFilename = $newName;

            // Delete old logo if it exists
            if (!empty($exhibitor['logo']) && file_exists(FCPATH . 'uploads/exhibitors/' . $exhibitor['logo'])) {
                unlink(FCPATH . 'uploads/exhibitors/' . $exhibitor['logo']);
            }
        }

        $data = [
            'company_name'     => $this->request->getPost('company_name'),
            'contact_person'   => $this->request->getPost('contact_person'),
            'email'            => $this->request->getPost('email'),
            'telephone'        => $this->request->getPost('telephone'),
            'website'          => $this->request->getPost('website'),
            'tagline'          => $this->request->getPost('tagline'),
            'vimeo_id'         => $this->request->getPost('vimeo_id'),
            'profile_summary'  => $this->request->getPost('profile_summary'),
            'has_promotion'    => $this->request->getPost('has_promotion') ? 1 : 0,
            'promotion_text'   => $this->request->getPost('promotion_text'),
            'logo'             => $logoFilename,
        ];

        $this->exhibitorsModel->update($id, $data);

        return redirect()->to(site_url('admin/exhibitors'))
            ->with('success', 'Exhibitor updated successfully.');
    }

    public function delete($id = null)
    {
        $exhibitor = $this->exhibitorsModel->find($id);
        if (! $exhibitor) {
            throw new PageNotFoundException('Exhibitor not found.');
        }

        if (!empty($exhibitor['logo']) && file_exists(FCPATH . 'uploads/exhibitors/' . $exhibitor['logo'])) {
            unlink(FCPATH . 'uploads/exhibitors/' . $exhibitor['logo']);
        }

        $this->exhibitorsModel->delete($id);

        return redirect()->to(site_url('admin/exhibitors'))
            ->with('success', 'Exhibitor deleted successfully.');
    }
}
