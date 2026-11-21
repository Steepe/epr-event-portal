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
    private const DEFAULT_CALLBACK_TOPICS = ['Programmes', 'Partnership', 'Speaking', 'Media'];

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

        $attendeeId = $this->attendeeId();
        $programmes = $db->table('tbl_emergence_booth_programmes')->where('is_active', 1)->orderBy('sort_order')->orderBy('title')->get()->getResultArray();
        $waitlistedProgrammeIds = [];
        if ($attendeeId > 0 && $programmes !== []) {
            $programmeIds = array_map(static fn (array $programme): int => (int) $programme['programme_id'], $programmes);
            foreach ($db->table('tbl_emergence_booth_programme_waitlists')
                ->select('programme_id')
                ->where('attendee_id', $attendeeId)
                ->whereIn('programme_id', $programmeIds)
                ->get()->getResultArray() as $waitlist) {
                $waitlistedProgrammeIds[(int) $waitlist['programme_id']] = true;
            }
        }
        foreach ($programmes as &$programme) {
            $programme['is_waitlisted'] = isset($waitlistedProgrammeIds[(int) $programme['programme_id']]);
        }
        unset($programme);

        $videoPlaylist = $this->videoPlaylist();
        return module_view('Web', 'emergence_booth', [
            'ready' => true,
            'members' => $members,
            'programmes' => $programmes,
            'resources' => $db->table('tbl_emergence_booth_resources')->where('is_active', 1)->orderBy('sort_order')->orderBy('title')->get()->getResultArray(),
            'videoPlaylist' => $videoPlaylist,
            'callbackTopics' => $this->callbackTopics(),
            'attendeeId' => $attendeeId,
        ]);
    }

    public function callback()
    {
        if (! $this->isReady()) {
            return $this->response->setStatusCode(503)->setJSON(['message' => 'The booth is still being prepared.']);
        }

        $topic = trim((string) $this->request->getPost('topic'));
        $note = trim((string) $this->request->getPost('note'));
        if (! in_array($topic, $this->callbackTopics(), true)) {
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

    public function joinProgrammeWaitlist(int $programmeId)
    {
        if (! $this->isReady()) {
            return $this->response->setStatusCode(503)->setJSON(['message' => 'The booth is still being prepared.']);
        }

        $attendeeId = $this->attendeeId();
        if ($attendeeId <= 0) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Please sign in to join the waitlist.']);
        }

        $db = db_connect();
        $programme = $db->table('tbl_emergence_booth_programmes')
            ->where('programme_id', $programmeId)
            ->where('is_active', 1)
            ->where('waitlist_enabled', 1)
            ->get()->getRowArray();
        if (! $programme) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'This programme is not accepting waitlist signups.']);
        }

        $waitlists = $db->table('tbl_emergence_booth_programme_waitlists');
        if ($waitlists->where('programme_id', $programmeId)->where('attendee_id', $attendeeId)->countAllResults() > 0) {
            return $this->response->setJSON(['status' => 'already_joined']);
        }

        try {
            $db->table('tbl_emergence_booth_programme_waitlists')->insert([
                'programme_id' => $programmeId,
                'attendee_id' => $attendeeId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Unable to add Emergence Booth programme waitlist signup: {message}', ['message' => $exception->getMessage()]);
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Unable to join the waitlist right now. Please try again.']);
        }

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

    private function callbackTopics(): array
    {
        $decoded = json_decode($this->setting('callback_topics'), true);
        if (! is_array($decoded)) {
            return self::DEFAULT_CALLBACK_TOPICS;
        }

        $topics = [];
        foreach ($decoded as $topic) {
            $topic = mb_substr(trim((string) $topic), 0, 100);
            if ($topic !== '') {
                $topics[] = $topic;
            }
        }

        return $topics === [] ? self::DEFAULT_CALLBACK_TOPICS : array_values(array_unique($topics));
    }

    private function videoPlaylist(): array
    {
        $decoded = json_decode($this->setting('video_playlist'), true);
        $playlist = is_array($decoded) ? $decoded : [];
        $playlist = array_values(array_filter($playlist, static fn ($url): bool => is_string($url) && filter_var($url, FILTER_VALIDATE_URL)));
        if ($playlist === []) {
            $legacy = $this->setting('welcome_video_url');
            if ($legacy !== '' && filter_var($legacy, FILTER_VALIDATE_URL)) {
                $playlist[] = $legacy;
            }
        }
        return array_slice($playlist, 0, 6);
    }

    private function isReady(): bool
    {
        $db = db_connect();
        foreach ([
            'tbl_emergence_booth_settings', 'tbl_emergence_booth_members', 'tbl_admin_presence',
            'tbl_emergence_booth_programmes', 'tbl_emergence_booth_resources',
            'tbl_emergence_booth_callback_requests', 'tbl_emergence_booth_messages',
            'tbl_emergence_booth_programme_waitlists',
        ] as $table) {
            if (! $db->tableExists($table)) {
                return false;
            }
        }
        return true;
    }

    private function attendeeId(): int
    {
        // user_id is the canonical account identifier. attendee_id remains as
        // a legacy fallback for sessions created before this was standardised.
        return (int) (session('user_id') ?: session('attendee_id'));
    }
}
