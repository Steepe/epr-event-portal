<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 06:20
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\AttendeeModel;
use App\Modules\MobileApp\Models\SpeakersModel;

class NetworkingCenter extends BaseController
{
    public function index()
    {
        // 🔒 Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $userId = (int) session('user_id');
        $tab    = $this->request->getGet('tab') === 'speakers' ? 'speakers' : 'attendees';

        $attendees = (new AttendeeModel())->getAttendees($userId);
        $speakers  = (new SpeakersModel())->getAllSpeakers();

        [$online, $connections] = $this->networkState($userId);

        foreach ($attendees as &$attendee) {
            $id                    = (int) $attendee['attendee_id'];
            $attendee['is_online'] = isset($online[$id]);
            $attendee['connection'] = $connections[$id] ?? 'none';
        }
        unset($attendee);

        return module_view('MobileApp', 'networking_center', [
            'activeTab' => $tab,
            'attendees' => $attendees,
            'speakers'  => $speakers,
        ]);
    }

    /**
     * @return array{0: array<int, bool>, 1: array<int, string>} online user ids and connection state per attendee
     */
    private function networkState(int $userId): array
    {
        $online      = [];
        $connections = [];

        try {
            $db = db_connect();

            if ($db->tableExists('tbl_portal_user_presence')) {
                $rows = $db->table('tbl_portal_user_presence')
                    ->select('user_id')
                    ->where('last_seen_at >=', date('Y-m-d H:i:s', time() - 120))
                    ->get()->getResultArray();
                foreach ($rows as $row) {
                    $online[(int) $row['user_id']] = true;
                }
            }

            if ($db->tableExists('tbl_attendee_connections')) {
                $rows = $db->table('tbl_attendee_connections')
                    ->select('requester_id, receiver_id, status')
                    ->groupStart()->where('requester_id', $userId)->orWhere('receiver_id', $userId)->groupEnd()
                    ->whereIn('status', ['pending', 'accepted'])
                    ->get()->getResultArray();
                foreach ($rows as $row) {
                    $sent  = (int) $row['requester_id'] === $userId;
                    $other = $sent ? (int) $row['receiver_id'] : (int) $row['requester_id'];
                    $connections[$other] = $row['status'] === 'accepted' ? 'connected' : ($sent ? 'pending' : 'incoming');
                }
            }
        } catch (\Throwable $e) {
            log_message('error', '[Mobile NetworkingCenter] ' . $e->getMessage());
        }

        return [$online, $connections];
    }
}
