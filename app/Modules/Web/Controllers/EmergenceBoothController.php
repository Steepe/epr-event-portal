<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 02:58
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class EmergenceBoothController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        if (! $this->isReady()) {
            return module_view('Web', 'emergence_booth', ['ready' => false]);
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

        return module_view('Web', 'emergence_booth', [
            'ready' => true,
            'members' => $members,
            'programmes' => $db->table('tbl_emergence_booth_programmes')->where('is_active', 1)->orderBy('sort_order')->orderBy('title')->get()->getResultArray(),
            'resources' => $db->table('tbl_emergence_booth_resources')->where('is_active', 1)->orderBy('sort_order')->orderBy('title')->get()->getResultArray(),
            'videoUrl' => $this->setting('welcome_video_url'),
            'attendeeId' => $this->attendeeId(),
        ]);
    }

    public function callback()
    {
        if (! $this->isReady()) {
            return $this->response->setStatusCode(503)->setJSON(['message' => 'The booth is still being prepared.']);
        }

        $topic = trim((string) $this->request->getPost('topic'));
        $note = trim((string) $this->request->getPost('note'));
        if (! in_array($topic, ['Programmes', 'Partnership', 'Speaking', 'Media'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Choose a callback topic.']);
        }

        db_connect()->table('tbl_emergence_booth_callback_requests')->insert([
            'attendee_id' => $this->attendeeId(),
            'topic' => $topic,
            'note' => mb_substr($note, 0, 5000),
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function chatMessages(int $memberId)
    {
        $member = $this->activeMember($memberId);
        if (! $member) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Booth team member not found.']);
        }

        $db = db_connect();
        $attendeeId = $this->attendeeId();
        $messages = $db->table('tbl_emergence_booth_messages')
            ->where('booth_member_id', $memberId)->where('attendee_id', $attendeeId)
            ->orderBy('created_at', 'ASC')->get()->getResultArray();
        $db->table('tbl_emergence_booth_messages')
            ->where('booth_member_id', $memberId)->where('attendee_id', $attendeeId)
            ->where('sender_type', 'admin')->where('read_at IS NULL', null, false)
            ->update(['read_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['data' => $messages]);
    }

    public function sendChat(int $memberId)
    {
        $member = $this->activeMember($memberId);
        $message = trim((string) $this->request->getPost('message'));
        if (! $member || $message === '' || mb_strlen($message) > 2000) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Enter a message of up to 2,000 characters.']);
        }

        db_connect()->table('tbl_emergence_booth_messages')->insert([
            'booth_member_id' => $memberId,
            'attendee_id' => $this->attendeeId(),
            'sender_type' => 'attendee',
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['status' => 'success']);
    }

    private function activeMember(int $memberId): ?array
    {
        if (! $this->isReady()) {
            return null;
        }
        return db_connect()->table('tbl_emergence_booth_members')
            ->where('booth_member_id', $memberId)->where('is_active', 1)
            ->get()->getRowArray();
    }

    private function setting(string $key): string
    {
        $row = db_connect()->table('tbl_emergence_booth_settings')->where('setting_key', $key)->get()->getRowArray();
        return (string) ($row['setting_value'] ?? '');
    }

    private function isReady(): bool
    {
        $db = db_connect();
        foreach ([
            'tbl_emergence_booth_settings', 'tbl_emergence_booth_members', 'tbl_admin_presence',
            'tbl_emergence_booth_programmes', 'tbl_emergence_booth_resources',
            'tbl_emergence_booth_callback_requests', 'tbl_emergence_booth_messages',
        ] as $table) {
            if (! $db->tableExists($table)) {
                return false;
            }
        }
        return true;
    }

    private function attendeeId(): int
    {
        return (int) (session('attendee_id') ?: session('user_id'));
    }
}
