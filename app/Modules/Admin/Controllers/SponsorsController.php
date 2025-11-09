<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 15:32
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\SponsorsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SponsorsController extends BaseController
{
    protected SponsorsModel $sponsorsModel;

    public function __construct()
    {
        $this->sponsorsModel = new SponsorsModel();
    }

    public function index()
    {
        $sponsors = $this->sponsorsModel
            ->orderBy('tier', 'ASC')
            ->orderBy('is_featured', 'DESC')
            ->findAll();

        echo module_view('Admin', 'sponsors/index', [
            'sponsors' => $sponsors,
            'pageTitle' => 'Sponsors',
        ]);
    }

    public function create()
    {
        echo module_view('Admin', 'sponsors/create', [
            'pageTitle' => 'Add Sponsor',
        ]);
    }

    public function store()
    {
        helper(['form', 'url']);

        $logo = $this->request->getFile('logo');
        $logoFilename = null;

        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/sponsors/', $newName);
            $logoFilename = $newName;
        }

        $data = [
            'name'         => $this->request->getPost('name'),
            'tier'         => $this->request->getPost('tier'),
            'is_featured'  => $this->request->getPost('is_featured') ? 1 : 0,
            'website'      => $this->request->getPost('website'),
            'description'  => $this->request->getPost('description'),
            'logo'         => $logoFilename,
        ];

        $this->sponsorsModel->insert($data);

        return redirect()->to(site_url('admin/sponsors'))
            ->with('success', 'Sponsor added successfully.');
    }

    public function edit($id = null)
    {
        $sponsor = $this->sponsorsModel->find($id);
        if (! $sponsor) {
            throw new PageNotFoundException('Sponsor not found.');
        }

        echo module_view('Admin', 'sponsors/edit', [
            'sponsor'   => $sponsor,
            'pageTitle' => 'Edit Sponsor - ' . $sponsor['name'],
        ]);
    }

    public function update($id = null)
    {
        helper(['form', 'url']);

        $sponsor = $this->sponsorsModel->find($id);
        if (! $sponsor) {
            throw new PageNotFoundException('Sponsor not found.');
        }

        $logo = $this->request->getFile('logo');
        $logoFilename = $sponsor['logo'];

        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/sponsors/', $newName);
            $logoFilename = $newName;

            if (!empty($sponsor['logo']) && file_exists(FCPATH . 'uploads/sponsors/' . $sponsor['logo'])) {
                unlink(FCPATH . 'uploads/sponsors/' . $sponsor['logo']);
            }
        }

        $data = [
            'name'         => $this->request->getPost('name'),
            'tier'         => $this->request->getPost('tier'),
            'is_featured'  => $this->request->getPost('is_featured') ? 1 : 0,
            'website'      => $this->request->getPost('website'),
            'description'  => $this->request->getPost('description'),
            'logo'         => $logoFilename,
        ];

        $this->sponsorsModel->update($id, $data);

        return redirect()->to(site_url('admin/sponsors'))
            ->with('success', 'Sponsor updated successfully.');
    }

    public function delete($id = null)
    {
        $sponsor = $this->sponsorsModel->find($id);
        if (! $sponsor) {
            throw new PageNotFoundException('Sponsor not found.');
        }

        if (!empty($sponsor['logo']) && file_exists(FCPATH . 'uploads/sponsors/' . $sponsor['logo'])) {
            unlink(FCPATH . 'uploads/sponsors/' . $sponsor['logo']);
        }

        $this->sponsorsModel->delete($id);
        return redirect()->to(site_url('admin/sponsors'))
            ->with('success', 'Sponsor deleted successfully.');
    }
}
