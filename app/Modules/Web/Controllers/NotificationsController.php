<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class NotificationsController extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $pendingConnections = [];
        $userId = (int) (session('user_id') ?: session('attendee_id'));

        try {
            $db = db_connect();
            if ($userId > 0 && $db->tableExists('tbl_attendee_connections')) {
                $pendingConnections = $db->table('tbl_attendee_connections c')
                    ->select('
                        c.id,
                        c.requester_id,
                        c.created_at,
                        a.firstname,
                        a.lastname,
                        a.company,
                        a.position,
                        u.email
                    ')
                    ->join('tbl_attendees a', 'a.attendee_id = c.requester_id', 'left')
                    ->join('tbl_users u', 'u.id = c.requester_id', 'left')
                    ->where('c.receiver_id', $userId)
                    ->where('c.status', 'pending')
                    ->orderBy('c.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('error', '[NotificationsController] Unable to load connection requests: ' . $e->getMessage());
        }

        return module_view('Web', 'notifications', [
            'page_title' => 'Notifications',
            'pendingConnections' => $pendingConnections,
        ]);
    }
}
