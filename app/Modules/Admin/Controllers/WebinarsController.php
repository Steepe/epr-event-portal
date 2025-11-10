<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 19:48
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\WebinarsModel;

class WebinarsController extends BaseController
{
    protected $webinarsModel;

    public function __construct()
    {
        $this->webinarsModel = new WebinarsModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Manage Webinars',
            'webinars'   => $this->webinarsModel->getAll()
        ];

        return module_view('Admin', 'webinars/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Add Webinar',
            'action'     => base_url('admin/webinars/store'),
        ];

        return module_view('Admin', 'webinars/create', $data);
    }

    public function store()
    {
        $this->webinarsModel->save($this->request->getPost());
        return redirect()->to(base_url('admin/webinars'))->with('success', 'Webinar created successfully.');
    }

    public function edit($id)
    {
        $webinar = $this->webinarsModel->find($id);
        if (!$webinar) return redirect()->back()->with('error', 'Webinar not found.');

        $data = [
            'page_title' => 'Edit Webinar',
            'webinar'    => $webinar,
            'action'     => base_url('admin/webinars/update/' . $id),
        ];

        return module_view('Admin', 'webinars/edit', $data);
    }

    public function update($id)
    {
        $this->webinarsModel->update($id, $this->request->getPost());
        return redirect()->to(base_url('admin/webinars'))->with('success', 'Webinar updated successfully.');
    }

    public function delete($id)
    {
        $this->webinarsModel->delete($id);
        return redirect()->to(base_url('admin/webinars'))->with('success', 'Webinar deleted successfully.');
    }

    public function toggleOpen($id)
    {
        if ($this->webinarsModel->toggleAccess($id)) {
            return redirect()->back()->with('success', 'Webinar access updated.');
        }
        return redirect()->back()->with('error', 'Unable to toggle webinar status.');
    }
}
