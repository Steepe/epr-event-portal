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
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected AdminModel $adminModel;
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->db = db_connect();
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

    public function forgotPassword(): string
    {
        return view('App\Modules\Admin\Views\auth_forgot_password');
    }

    public function sendResetLink(): \CodeIgniter\HTTP\RedirectResponse
    {
        $email = trim((string) $this->request->getPost('email'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid email address.');
        }

        $admin = $this->adminModel->findByEmail($email);
        if ($admin && ($admin['status'] ?? null) === 'active') {
            $token = bin2hex(random_bytes(32));
            $this->storeResetToken((int) $admin['id'], $admin['email'], $token);
            $this->sendResetEmail($admin['email'], $admin['name'] ?? 'Admin', $token);
        }

        return redirect()->to(site_url('admin/login'))
            ->with('success', 'If that email belongs to an active admin, a reset link has been sent.');
    }

    public function resetPasswordForm(string $token): \CodeIgniter\HTTP\RedirectResponse|string
    {
        if (! $this->getValidResetRecord($token)) {
            return redirect()->to(site_url('admin/login'))
                ->with('login_error', 'Invalid or expired reset link.');
        }

        return view('App\Modules\Admin\Views\auth_reset_password', [
            'token' => $token,
        ]);
    }

    public function resetPassword(string $token): \CodeIgniter\HTTP\RedirectResponse
    {
        $password = (string) $this->request->getPost('password');
        $confirm = (string) $this->request->getPost('password_confirm');

        if (strlen($password) < 8) {
            return redirect()->back()
                ->with('error', 'Password must be at least 8 characters.');
        }

        if ($password !== $confirm) {
            return redirect()->back()
                ->with('error', 'Passwords do not match.');
        }

        $record = $this->getValidResetRecord($token);
        if (! $record) {
            return redirect()->to(site_url('admin/login'))
                ->with('login_error', 'Invalid or expired reset link.');
        }

        $updated = $this->adminModel->update((int) $record['admin_id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if (! $updated) {
            log_message('error', 'Admin password reset failed for admin ID ' . $record['admin_id'] . ': ' . json_encode($this->adminModel->errors()));

            return redirect()->back()
                ->with('error', 'Unable to update password. Please request a new reset link.');
        }

        $this->db->table('admin_password_resets')
            ->where('id', $record['id'])
            ->update(['used_at' => date('Y-m-d H:i:s')]);

        $this->db->table('admin_password_resets')
            ->where('admin_id', $record['admin_id'])
            ->where('used_at IS NULL', null, false)
            ->delete();

        return redirect()->to(site_url('admin/login'))
            ->with('success', 'Password reset successfully. You can now sign in.');
    }

    private function storeResetToken(int $adminId, string $email, string $token): void
    {
        $this->db->table('admin_password_resets')
            ->where('admin_id', $adminId)
            ->where('used_at IS NULL', null, false)
            ->delete();

        $this->db->table('admin_password_resets')->insert([
            'admin_id'   => $adminId,
            'email'      => $email,
            'token_hash' => hash('sha256', $token),
            'expires_at' => Time::now()->addHours(1)->toDateTimeString(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getValidResetRecord(string $token): ?array
    {
        if (! preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return null;
        }

        $record = $this->db->table('admin_password_resets')
            ->where('token_hash', hash('sha256', $token))
            ->where('used_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        if (! $record || strtotime($record['expires_at']) < time()) {
            return null;
        }

        return $record;
    }

    private function sendResetEmail(string $email, string $name, string $token): void
    {
        $resetLink = site_url('admin/reset-password/' . $token);
        $emailService = service('email');

        $emailService->setFrom(
            env('email.fromEmail') ?: 'no-reply@portal.eprglobal.com',
            env('email.fromName') ?: 'EPR Global Events'
        );
        $emailService->setTo($email);
        $emailService->setSubject('Admin Password Reset Request');
        $emailService->setMailType('html');
        $emailService->setMessage(view('App\Modules\Admin\Views\emails\password_reset', [
            'name' => $name,
            'resetLink' => $resetLink,
        ]));

        if (! $emailService->send()) {
            log_message('error', 'Admin reset email failed for ' . $email . ': ' . print_r($emailService->printDebugger(['headers']), true));
        }
    }
}
