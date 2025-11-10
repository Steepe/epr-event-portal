<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 10:07
 */

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;

class ForgotPasswordController extends BaseController
{
    public function index()
    {
        return module_view('Web', 'auth/forgot_password');
    }

    public function sendLink()
    {
        $email = $this->request->getPost('email');
        $db = db_connect();

        // Verify the user exists
        $user = $db->table('tbl_users')->where('email', $email)->get()->getRowArray();

        if (!$user) {
            return redirect()->back()->with('error', 'No account found with that email.');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(1)->toDateTimeString();

        // Store token
        $db->table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expires
        ]);

        // Send reset email (simplified)
        $resetLink = base_url("attendees/reset-password/{$token}");
        $message = "Hello,\n\nClick the link below to reset your password:\n\n{$resetLink}\n\nThis link expires in 1 hour.";

        // Use your email service
        service('email')
            ->setTo($email)
            ->setSubject('Password Reset Request')
            ->setMessage($message)
            ->send();

        return redirect()->to(base_url('attendees/login'))->with('success', 'Password reset link sent to your email.');
    }

    public function resetForm($token)
    {
        $db = db_connect();
        $record = $db->table('password_resets')->where('token', $token)->get()->getRowArray();

        if (!$record || strtotime($record['expires_at']) < time()) {
            return redirect()->to(base_url('attendees/login'))->with('error', 'Invalid or expired token.');
        }

        return module_view('Web', 'auth/reset_password', ['token' => $token]);
    }

    public function updatePassword($token)
    {
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm');

        if ($password !== $confirm) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $db = db_connect();
        $record = $db->table('password_resets')->where('token', $token)->get()->getRowArray();

        if (!$record || strtotime($record['expires_at']) < time()) {
            return redirect()->to(base_url('attendees/login'))->with('error', 'Invalid or expired token.');
        }

        // Update password in tbl_users
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db->table('tbl_users')->where('email', $record['email'])->update(['password' => $hash]);

        // Remove used token
        $db->table('password_resets')->where('email', $record['email'])->delete();

        return redirect()->to(base_url('attendees/login'))->with('success', 'Password successfully reset. You can now log in.');
    }
}
