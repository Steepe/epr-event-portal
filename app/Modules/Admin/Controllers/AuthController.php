<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:13
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\AdminModel;

class AuthController extends BaseController
{
    protected AdminModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function login()
    {
        if (session()->get('is_admin')) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        echo view('App\Modules\Admin\Views\auth_login');
    }

    public function attempt()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $admin = $this->adminModel->verifyCredentials($email, $password);

        if ($admin) {
            session()->set([
                'admin_id'    => $admin['id'],
                'admin_name'  => $admin['name'],
                'admin_email' => $admin['email'],
                'admin_role'  => $admin['role'],
                'is_admin'    => true,
            ]);

            $this->adminModel->markLogin($admin['id']);

            return redirect()->to(site_url('admin/dashboard'));
        }

        session()->setFlashdata('login_error', 'Invalid credentials or disabled account.');
        return redirect()->to(site_url('admin/login'));
    }

    public function logout()
    {
        session()->remove(['admin_id','admin_name','admin_email','admin_role','is_admin']);
        return redirect()->to(site_url('admin/login'));
    }
}
