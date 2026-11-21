<?php

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Services\BunnyStreamService;
use CodeIgniter\Exceptions\PageNotFoundException;

class EmergenceBoothController extends BaseController
{
    private const DEFAULT_CALLBACK_TOPICS = ['Programmes', 'Partnership', 'Speaking', 'Media'];
    private const MAX_PLAYLIST_VIDEOS = 6;

    private const TABLES = [
        'tbl_emergence_booth_settings',
        'tbl_emergence_booth_members',
        'tbl_admin_presence',
        'tbl_emergence_booth_programmes',
        'tbl_emergence_booth_resources',
        'tbl_emergence_booth_callback_requests',
        'tbl_emergence_booth_messages',
        'tbl_emergence_booth_programme_waitlists',
    ];

    public function index()
    {
        if (! $this->isReady()) {
            return module_view('Admin', 'emergence_booth/index', ['ready' => false]);
        }

        $db = db_connect();

        $programmes = $db->table('tbl_emergence_booth_programmes')->orderBy('sort_order')->orderBy('title')->get()->getResultArray();
        $waitlistCounts = [];
        foreach ($db->table('tbl_emergence_booth_programme_waitlists')->select('programme_id, COUNT(*) AS total')->groupBy('programme_id')->get()->getResultArray() as $count) {
            $waitlistCounts[(int) $count['programme_id']] = (int) $count['total'];
        }
        foreach ($programmes as &$programme) {
            $programme['waitlist_count'] = $waitlistCounts[(int) $programme['programme_id']] ?? 0;
        }
        unset($programme);

        return module_view('Admin', 'emergence_booth/index', [
            'ready' => true,
            'admins' => $db->table('tbl_admins')->where('status', 'active')->orderBy('name')->get()->getResultArray(),
            'members' => $this->members(),
            'programmes' => $programmes,
            'resources' => $db->table('tbl_emergence_booth_resources')->orderBy('sort_order')->orderBy('title')->get()->getResultArray(),
            'callbacks' => $this->callbacks(),
            'callbackTopics' => $this->callbackTopics(),
            'programmeWaitlists' => $this->programmeWaitlists(),
            'videoUrl' => $this->setting('welcome_video_url'),
            'videoPlaylist' => $this->videoPlaylist(),
        ]);
    }

    public function saveVideo()
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        try {
            $playlist = $this->videoPlaylist();
            $availableSlots = self::MAX_PLAYLIST_VIDEOS - count($playlist);
            if ($availableSlots <= 0) {
                return redirect()->back()->with('error', 'The booth playlist can contain up to six videos. Remove a video before adding another.');
            }

            $files = array_values(array_filter(
                $this->request->getFileMultiple('booth_videos'),
                static fn ($file): bool => $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE
            ));
            if ($files === []) {
                return redirect()->back()->with('error', 'Choose at least one video to upload.');
            }
            if (count($files) > $availableSlots) {
                return redirect()->back()->with('error', sprintf(
                    'You selected %d videos, but this playlist has room for %d more.',
                    count($files),
                    $availableSlots
                ));
            }

            $bunny = new BunnyStreamService();
            foreach ($files as $file) {
                $playlist[] = $bunny->uploadWelcomeVideo($file, 'Emergence Booth Video ' . (count($playlist) + 1));
            }
            $this->saveSetting('video_playlist', json_encode($playlist, JSON_UNESCAPED_SLASHES));
            $this->saveSetting('welcome_video_url', $playlist[0]);
        } catch (\RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to(site_url('admin/emergence-booth'))->with('success', sprintf('%d booth video%s added to the Bunny Stream playlist.', count($files), count($files) === 1 ? '' : 's'));
    }

    public function removeVideo(int $index)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $playlist = $this->videoPlaylist();
        if (! array_key_exists($index, $playlist)) {
            return redirect()->back()->with('error', 'That video is not in the booth playlist.');
        }

        array_splice($playlist, $index, 1);
        $this->saveSetting('video_playlist', json_encode(array_values($playlist), JSON_UNESCAPED_SLASHES));
        $this->saveSetting('welcome_video_url', $playlist[0] ?? '');
        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Booth video removed from the playlist.');
    }

    public function moveVideo(int $index)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $playlist = $this->videoPlaylist();
        $direction = (string) $this->request->getPost('direction');
        $destination = $direction === 'up' ? $index - 1 : ($direction === 'down' ? $index + 1 : $index);
        if (! array_key_exists($index, $playlist) || ! array_key_exists($destination, $playlist)) {
            return redirect()->back()->with('error', 'That playlist move is not available.');
        }

        [$playlist[$index], $playlist[$destination]] = [$playlist[$destination], $playlist[$index]];
        $this->saveSetting('video_playlist', json_encode(array_values($playlist), JSON_UNESCAPED_SLASHES));
        $this->saveSetting('welcome_video_url', $playlist[0] ?? '');

        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Booth video order updated.');
    }

    public function saveMember()
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $db = db_connect();
        $adminId = (int) $this->request->getPost('admin_id');
        $admin = $db->table('tbl_admins')->where('id', $adminId)->where('status', 'active')->get()->getRowArray();
        if (! $admin) {
            return redirect()->back()->with('error', 'Choose an active admin account.');
        }

        $data = [
            'admin_id' => $adminId,
            'display_name' => $this->limitedPost('display_name', 255) ?: (string) $admin['name'],
            'title' => $this->limitedPost('title', 255),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $db->table('tbl_emergence_booth_members')->where('admin_id', $adminId)->get()->getRowArray();
        if ($existing) {
            $db->table('tbl_emergence_booth_members')->where('booth_member_id', $existing['booth_member_id'])->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('tbl_emergence_booth_members')->insert($data);
        }

        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Booth team member saved.');
    }

    public function deleteMember(int $id)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        db_connect()->table('tbl_emergence_booth_members')->where('booth_member_id', $id)->delete();
        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Booth team member removed.');
    }

    public function saveProgramme(?int $id = null)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $title = $this->limitedPost('title', 255);
        if ($title === '') {
            return redirect()->back()->with('error', 'Programme title is required.');
        }

        $db = db_connect();
        $existing = $id ? $db->table('tbl_emergence_booth_programmes')->where('programme_id', $id)->get()->getRowArray() : null;
        $flyerPath = (string) ($existing['flyer_path'] ?? '');
        $flyerName = (string) ($existing['flyer_name'] ?? '');
        $flyer = $this->request->getFile('flyer');

        if ($flyer && $flyer->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $flyer->isValid() || $flyer->hasMoved()) {
                return redirect()->back()->with('error', 'The programme flyer upload failed.');
            }
            if (! in_array(strtolower((string) $flyer->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return redirect()->back()->with('error', 'Upload a JPG, PNG, or WebP programme flyer.');
            }
            if ($flyer->getSize() > 10 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Programme flyers must be 10 MB or smaller.');
            }

            $directory = FCPATH . 'uploads/emergence_programmes/';
            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                return redirect()->back()->with('error', 'Unable to prepare the programme flyer upload folder.');
            }
            $newName = $flyer->getRandomName();
            $flyer->move($directory, $newName);
            if ($flyerPath !== '' && is_file(FCPATH . ltrim($flyerPath, '/'))) {
                unlink(FCPATH . ltrim($flyerPath, '/'));
            }
            $flyerPath = 'uploads/emergence_programmes/' . $newName;
            $flyerName = $flyer->getClientName();
        }

        $data = [
            'title' => $title,
            'label' => $this->limitedPost('label', 255),
            'description' => $this->limitedPost('description', 5000),
            'icon' => $this->limitedPost('icon', 100) ?: 'fa-rocket',
            'flyer_path' => $flyerPath ?: null,
            'flyer_name' => $flyerName ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'waitlist_enabled' => $this->request->getPost('waitlist_enabled') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $table = $db->table('tbl_emergence_booth_programmes');
        if ($id) {
            $table->where('programme_id', $id)->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $table->insert($data);
        }

        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Programme saved.');
    }

    public function deleteProgramme(int $id)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $db = db_connect();
        $programme = $db->table('tbl_emergence_booth_programmes')->where('programme_id', $id)->get()->getRowArray();
        if ($programme && ! empty($programme['flyer_path'])) {
            $path = FCPATH . ltrim((string) $programme['flyer_path'], '/');
            if (is_file($path)) {
                unlink($path);
            }
        }
        $db->table('tbl_emergence_booth_programme_waitlists')->where('programme_id', $id)->delete();
        $db->table('tbl_emergence_booth_programmes')->where('programme_id', $id)->delete();
        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Programme deleted.');
    }

    public function saveResource(?int $id = null)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $db = db_connect();
        $title = $this->limitedPost('title', 255);
        if ($title === '') {
            return redirect()->back()->with('error', 'Resource title is required.');
        }

        $existing = $id ? $db->table('tbl_emergence_booth_resources')->where('resource_id', $id)->get()->getRowArray() : null;
        $filePath = $existing['file_path'] ?? '';
        $fileName = $existing['file_name'] ?? '';
        $file = $this->request->getFile('resource_file');

        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $file->isValid() || $file->hasMoved()) {
                return redirect()->back()->with('error', 'The resource upload failed.');
            }
            if (! in_array(strtolower((string) $file->getExtension()), ['pdf', 'xlsx', 'xls', 'docx', 'pptx', 'zip'], true)) {
                return redirect()->back()->with('error', 'Upload a PDF, spreadsheet, document, presentation, or ZIP file.');
            }
            if ($file->getSize() > 25 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Resources must be 25 MB or smaller.');
            }

            $directory = FCPATH . 'uploads/emergence_toolkit/';
            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                return redirect()->back()->with('error', 'Unable to prepare the resource upload folder.');
            }
            $newName = $file->getRandomName();
            $file->move($directory, $newName);
            $filePath = 'uploads/emergence_toolkit/' . $newName;
            $fileName = $file->getClientName();
        }

        if ($filePath === '') {
            return redirect()->back()->with('error', 'Choose a resource file to upload.');
        }

        $data = [
            'title' => $title,
            'meta' => $this->limitedPost('meta', 255),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $table = $db->table('tbl_emergence_booth_resources');
        if ($id) {
            $table->where('resource_id', $id)->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $table->insert($data);
        }

        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Toolkit resource saved.');
    }

    public function deleteResource(int $id)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $db = db_connect();
        $resource = $db->table('tbl_emergence_booth_resources')->where('resource_id', $id)->get()->getRowArray();
        if ($resource) {
            $path = FCPATH . ltrim((string) $resource['file_path'], '/');
            if (is_file($path)) {
                unlink($path);
            }
            $db->table('tbl_emergence_booth_resources')->where('resource_id', $id)->delete();
        }

        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Toolkit resource deleted.');
    }

    public function updateCallback(int $id)
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['new', 'in_progress', 'completed'], true)) {
            return redirect()->back()->with('error', 'Choose a valid callback status.');
        }

        db_connect()->table('tbl_emergence_booth_callback_requests')
            ->where('callback_request_id', $id)
            ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Callback request updated.');
    }

    public function saveCallbackTopics()
    {
        if ($response = $this->notReadyResponse()) {
            return $response;
        }

        $submitted = $this->request->getPost('topics');
        $topics = [];
        $seen = [];
        foreach (is_array($submitted) ? $submitted : [] as $topic) {
            $topic = mb_substr(trim((string) $topic), 0, 100);
            $key = mb_strtolower($topic);
            if ($topic === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $topics[] = $topic;
        }

        if ($topics === []) {
            return redirect()->back()->with('error', 'Add at least one callback topic.');
        }

        $this->saveSetting('callback_topics', json_encode($topics, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return redirect()->to(site_url('admin/emergence-booth'))->with('success', 'Callback topics saved.');
    }

    public function chats()
    {
        if (! $this->isReady()) {
            return module_view('Admin', 'emergence_booth/chats', ['ready' => false]);
        }

        $member = $this->currentMember();
        $threads = $member ? $this->threads((int) $member['booth_member_id']) : [];
        $attendeeId = (int) $this->request->getGet('attendee');
        if ($attendeeId > 0 && ! array_key_exists($attendeeId, $threads)) {
            $attendeeId = 0;
        }

        return module_view('Admin', 'emergence_booth/chats', [
            'ready' => true,
            'member' => $member,
            'threads' => $threads,
            'selectedAttendeeId' => $attendeeId,
        ]);
    }

    public function chatMessages(int $attendeeId)
    {
        $member = $this->currentMember();
        if (! $member) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'You are not an active booth team member.']);
        }

        return $this->response->setJSON(['data' => $this->messages((int) $member['booth_member_id'], $attendeeId)]);
    }

    public function sendChat(int $attendeeId)
    {
        $member = $this->currentMember();
        $message = trim((string) $this->request->getPost('message'));
        if (! $member || $message === '' || mb_strlen($message) > 2000) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Enter a message of up to 2,000 characters.']);
        }

        db_connect()->table('tbl_emergence_booth_messages')->insert([
            'booth_member_id' => (int) $member['booth_member_id'],
            'attendee_id' => $attendeeId,
            'sender_type' => 'admin',
            'sender_admin_id' => (int) session('admin_id'),
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function heartbeat()
    {
        if ($this->isReady()) {
            $this->touchPresence((int) session('admin_id'));
        }

        return $this->response->setJSON(['status' => 'ok']);
    }

    private function members(): array
    {
        $rows = db_connect()->table('tbl_emergence_booth_members m')
            ->select('m.*, a.email, p.last_seen_at')
            ->join('tbl_admins a', 'a.id = m.admin_id', 'left')
            ->join('tbl_admin_presence p', 'p.admin_id = m.admin_id', 'left')
            ->orderBy('m.sort_order')->orderBy('m.display_name')
            ->get()->getResultArray();

        foreach ($rows as &$row) {
            $row['is_online'] = ! empty($row['last_seen_at']) && strtotime((string) $row['last_seen_at']) >= time() - 120;
        }
        unset($row);
        return $rows;
    }

    private function callbacks(): array
    {
        return db_connect()->table('tbl_emergence_booth_callback_requests r')
            ->select('r.*, a.firstname, a.lastname, a.telephone, a.country, u.email')
            ->join('tbl_attendees a', 'a.attendee_id = r.attendee_id', 'left')
            ->join('tbl_users u', 'u.id = r.attendee_id', 'left')
            ->orderBy('r.created_at', 'DESC')
            ->get()->getResultArray();
    }

    private function programmeWaitlists(): array
    {
        return db_connect()->table('tbl_emergence_booth_programme_waitlists w')
            ->select('w.*, p.title AS programme_title, a.firstname, a.lastname, a.telephone, a.country, u.email')
            ->join('tbl_emergence_booth_programmes p', 'p.programme_id = w.programme_id', 'left')
            ->join('tbl_attendees a', 'a.attendee_id = w.attendee_id', 'left')
            ->join('tbl_users u', 'u.id = w.attendee_id', 'left')
            ->orderBy('w.created_at', 'DESC')
            ->get()->getResultArray();
    }

    private function currentMember(): ?array
    {
        if (! $this->isReady()) {
            return null;
        }

        return db_connect()->table('tbl_emergence_booth_members')
            ->where('admin_id', (int) session('admin_id'))
            ->where('is_active', 1)
            ->get()->getRowArray();
    }

    private function threads(int $memberId): array
    {
        $rows = db_connect()->table('tbl_emergence_booth_messages')
            ->where('booth_member_id', $memberId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
        $threads = [];
        foreach ($rows as $row) {
            $id = (int) $row['attendee_id'];
            if (! isset($threads[$id])) {
                $threads[$id] = ['attendee_id' => $id, 'last_message' => $row['message'], 'last_at' => $row['created_at'], 'unread' => 0];
            }
            if ($row['sender_type'] === 'attendee' && empty($row['read_at'])) {
                ++$threads[$id]['unread'];
            }
        }
        if ($threads === []) {
            return [];
        }

        $attendees = db_connect()->table('tbl_attendees a')
            ->select('a.attendee_id, a.firstname, a.lastname, u.email')
            ->join('tbl_users u', 'u.id = a.attendee_id', 'left')
            ->whereIn('a.attendee_id', array_keys($threads))
            ->get()->getResultArray();
        foreach ($attendees as $attendee) {
            $id = (int) $attendee['attendee_id'];
            $threads[$id]['name'] = trim(($attendee['firstname'] ?? '') . ' ' . ($attendee['lastname'] ?? '')) ?: 'Attendee';
            $threads[$id]['email'] = $attendee['email'] ?? '';
        }
        return $threads;
    }

    private function messages(int $memberId, int $attendeeId): array
    {
        $db = db_connect();
        $rows = $db->table('tbl_emergence_booth_messages')
            ->where('booth_member_id', $memberId)->where('attendee_id', $attendeeId)
            ->orderBy('created_at', 'ASC')->get()->getResultArray();
        $db->table('tbl_emergence_booth_messages')
            ->where('booth_member_id', $memberId)->where('attendee_id', $attendeeId)
            ->where('sender_type', 'attendee')->where('read_at IS NULL', null, false)
            ->update(['read_at' => date('Y-m-d H:i:s')]);
        return $rows;
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
        return array_slice($playlist, 0, self::MAX_PLAYLIST_VIDEOS);
    }

    private function saveSetting(string $key, string $value): void
    {
        $table = db_connect()->table('tbl_emergence_booth_settings');
        $existing = $table->where('setting_key', $key)->get()->getRowArray();
        $data = ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')];
        $existing ? $table->where('setting_key', $key)->update($data) : $table->insert(['setting_key' => $key] + $data);
    }

    private function touchPresence(int $adminId): void
    {
        if ($adminId <= 0) {
            return;
        }
        $table = db_connect()->table('tbl_admin_presence');
        $existing = $table->where('admin_id', $adminId)->get()->getRowArray();
        $existing ? $table->where('admin_id', $adminId)->update(['last_seen_at' => date('Y-m-d H:i:s')]) : $table->insert(['admin_id' => $adminId, 'last_seen_at' => date('Y-m-d H:i:s')]);
    }

    private function isReady(): bool
    {
        $db = db_connect();
        foreach (self::TABLES as $table) {
            if (! $db->tableExists($table)) {
                return false;
            }
        }
        return true;
    }

    private function notReadyResponse()
    {
        return $this->isReady() ? null : redirect()->to(site_url('admin/emergence-booth'))->with('error', 'The Emergence Booth migration must be run before this feature can be used.');
    }

    private function limitedPost(string $field, int $limit): string
    {
        return mb_substr(trim((string) $this->request->getPost($field)), 0, $limit);
    }
}
