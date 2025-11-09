<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 17:15
 */


namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class AdminsController extends BaseController
{
    protected mixed $adminModel;

    public function __construct()
    {
        $this->adminModel = model('App\Modules\Admin\Models\AdminModel');
        // 🔐 Restrict access: Only superadmins allowed
        if (session('admin_role') !== 'superadmin') {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function index()
    {
        $admins = $this->adminModel->orderBy('created_at', 'DESC')->findAll();

        echo module_view('Admin', 'admins/index', [
            'admins' => $admins,
            'pageTitle' => 'Manage Admin Users'
        ]);
    }

    public function create()
    {
        echo module_view('Admin', 'admins/create', [
            'pageTitle' => 'Add Admin User'
        ]);
    }

    public function store()
    {
        helper(['form', 'url']);

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT, ['cost' => 12]),
            'role'     => $this->request->getPost('role'),
            'status'   => 'active'
        ];

        $this->adminModel->insert($data);

        return redirect()->to(site_url('admin/admins'))
            ->with('success', 'Admin user created successfully.');
    }

    public function edit($id)
    {
        $admin = $this->adminModel->find($id);
        if (! $admin) {
            throw new PageNotFoundException('Admin not found.');
        }

        echo module_view('Admin', 'admins/edit', [
            'admin' => $admin,
            'pageTitle' => 'Edit Admin User'
        ]);
    }

    public function update($id)
    {
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $this->adminModel->update($id, $data);

        return redirect()->to(site_url('admin/admins'))
            ->with('success', 'Admin updated successfully.');
    }

    public function toggle($id)
    {
        $admin = $this->adminModel->find($id);
        if (! $admin) throw new PageNotFoundException();

        $newStatus = ($admin['status'] === 'active') ? 'disabled' : 'active';
        $this->adminModel->update($id, ['status' => $newStatus]);

        return redirect()->back()->with('success', 'Status changed to ' . ucfirst($newStatus));
    }

    public function delete($id)
    {
        $this->adminModel->delete($id);
        return redirect()->back()->with('success', 'Admin deleted successfully.');
    }
}
