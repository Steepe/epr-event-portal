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
        $tab    = (string) $this->request->getGet('tab');
        $tab    = in_array($tab, ['speakers', 'communities'], true) ? $tab : 'attendees';

        $attendees = (new AttendeeModel())->getAttendees($userId);
        $speakers  = (new SpeakersModel())->getAllSpeakers();

        [$online, $connections] = $this->networkState($userId);

        foreach ($attendees as &$attendee) {
            $id                    = (int) $attendee['attendee_id'];
            $attendee['is_online'] = isset($online[$id]);
            $attendee['connection'] = $connections[$id] ?? 'none';
        }
        unset($attendee);

        // A–Z by first name then last name, ignoring case and stray spaces; unnamed attendees go last.
        $sortKey = static fn (array $row): string => mb_strtolower(trim(trim((string) ($row['firstname'] ?? '')) . ' ' . trim((string) ($row['lastname'] ?? ''))));
        usort($attendees, static function (array $a, array $b) use ($sortKey): int {
            $nameA = $sortKey($a);
            $nameB = $sortKey($b);

            if (($nameA === '') !== ($nameB === '')) {
                return $nameA === '' ? 1 : -1;
            }

            return strnatcmp($nameA, $nameB) ?: ((int) $a['attendee_id'] <=> (int) $b['attendee_id']);
        });

        return module_view('MobileApp', 'networking_center', [
            'activeTab' => $tab,
            'attendees' => $attendees,
            'speakers'  => $speakers,
        ] + $this->communities());
    }

    /**
     * mobile/communities opens the Networking Center on its Communities tab.
     */
    public function communitiesTab()
    {
        return redirect()->to(site_url('mobile/networking-center?tab=communities'));
    }

    /**
     * Country communities for the Communities tab: each active country with its manager,
     * its WhatsApp link or QR code, and how many manager replies this attendee has not read.
     * The attendee's own country is listed first.
     *
     * @return array{communitiesReady: bool, communities: list<array<string, mixed>>}
     */
    private function communities(): array
    {
        try {
            $db = db_connect();

            if (! $db->tableExists('tbl_country_communities') || ! $db->tableExists('tbl_country_community_messages')) {
                return ['communitiesReady' => false, 'communities' => []];
            }

            $communities = $db->table('tbl_country_communities c')
                ->select('c.community_id, c.country, c.manager_title, c.manager_photo, c.whatsapp_url, c.whatsapp_qr_path, a.name AS manager_name')
                ->join('tbl_admins a', 'a.id = c.manager_admin_id', 'left')
                ->where('c.is_active', 1)
                ->orderBy('c.sort_order')
                ->orderBy('c.country')
                ->get()->getResultArray();

            // Same attendee id the chat endpoints store messages under.
            $attendeeId = (int) (session('attendee_id') ?: session('user_id'));
            $unread     = [];
            $rows       = $db->table('tbl_country_community_messages')
                ->select('community_id, COUNT(*) AS total')
                ->where('attendee_id', $attendeeId)
                ->where('sender_type', 'admin')
                ->where('read_at IS NULL', null, false)
                ->groupBy('community_id')
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $unread[(int) $row['community_id']] = (int) $row['total'];
            }

            $home = mb_strtolower(trim((string) session('reg_country')));
            foreach ($communities as &$community) {
                $community['unread']  = $unread[(int) $community['community_id']] ?? 0;
                $community['is_home'] = $home !== '' && mb_strtolower(trim((string) $community['country'])) === $home;
            }
            unset($community);

            // Stable: keeps the admin's display order within each group.
            usort($communities, static fn (array $a, array $b): int => (int) $b['is_home'] <=> (int) $a['is_home']);

            return ['communitiesReady' => true, 'communities' => $communities];
        } catch (\Throwable $e) {
            log_message('error', '[Mobile NetworkingCenter] ' . $e->getMessage());

            return ['communitiesReady' => false, 'communities' => []];
        }
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
