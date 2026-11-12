<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 12:04
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;

class EmergenceBoothController extends BaseController
{
    private const TABLES = [
        'tbl_emergence_booth_settings', 'tbl_emergence_booth_members', 'tbl_admin_presence',
        'tbl_emergence_booth_programmes', 'tbl_emergence_booth_resources',
        'tbl_emergence_booth_callback_requests', 'tbl_emergence_booth_messages',
    ];

    /**
     * Booth content is managed in the admin and shared with the desktop portal.
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $db = db_connect();

        foreach (self::TABLES as $table) {
            if (! $db->tableExists($table)) {
                return module_view('MobileApp', 'emergence_booth', ['ready' => false]);
            }
        }

        $members = $db->table('tbl_emergence_booth_members m')
            ->select('m.*, p.last_seen_at')
            ->join('tbl_admin_presence p', 'p.admin_id = m.admin_id', 'left')
            ->where('m.is_active', 1)
            ->orderBy('m.sort_order')->orderBy('m.display_name')
            ->get()->getResultArray();

        foreach ($members as &$member) {
            $member['is_online'] = ! empty($member['last_seen_at']) && strtotime((string) $member['last_seen_at']) >= time() - 120;
        }
        unset($member);

        $video = $db->table('tbl_emergence_booth_settings')->where('setting_key', 'welcome_video_url')->get()->getRowArray();

        return module_view('MobileApp', 'emergence_booth', [
            'ready'      => true,
            'members'    => $members,
            'programmes' => $db->table('tbl_emergence_booth_programmes')->where('is_active', 1)->orderBy('sort_order')->orderBy('title')->get()->getResultArray(),
            'resources'  => $db->table('tbl_emergence_booth_resources')->where('is_active', 1)->orderBy('sort_order')->orderBy('title')->get()->getResultArray(),
            'videoUrl'   => (string) ($video['setting_value'] ?? ''),
        ]);
    }

    public function sendSupportEmail()
    {
        $request = service('request');
        $data = $request->getJSON(true);

        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required.']);
        }

        $email = \Config\Services::email();

        // 📨 Load settings from .env automatically
        $email->setFrom(env('email.fromEmail'), env('email.fromName'));
        $email->setTo('registration@eprglobal.com');
        $email->setReplyTo($data['email'], $data['name']);

        // ✉️ Subject and Body
        $subject = 'Message from ' . $data['name'] . ' <' . $data['email'] . '>';
        $body = "
            <div style='font-family: Arial, sans-serif; font-size: 15px; color: #333;'>
                <p><strong>Name:</strong> {$data['name']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <hr>
                <p style='margin-top: 10px;'><strong>Message:</strong></p>
                <p>" . nl2br(esc($data['message'])) . "</p>
            </div>
        ";

        $email->setSubject($subject);
        $email->setMessage($body);

        if ($email->send()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Your message has been sent successfully!'
            ]);
        }

        // Log and show email debug if sending fails
        log_message('error', 'Support email failed: ' . $email->printDebugger(['headers']));
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unable to send message. Please try again later.'
        ]);
    }

}
