<?php

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class CommunitiesController extends BaseController
{
    private const COMMUNITY_TABLE = 'tbl_country_communities';
    private const MESSAGE_TABLE = 'tbl_country_community_messages';

    public function index(): string
    {
        if (! $this->isReady()) {
            return module_view('Admin', 'communities/index', ['ready' => false]);
        }

        $db = db_connect();
        $canManage = $this->isSuperadmin();
        $builder = $db->table(self::COMMUNITY_TABLE . ' c')
            ->select('c.*, a.name AS manager_name, a.email AS manager_email')
            ->join('tbl_admins a', 'a.id = c.manager_admin_id', 'left')
            ->orderBy('c.sort_order')
            ->orderBy('c.country');

        if (! $canManage) {
            $builder->where('c.manager_admin_id', (int) session('admin_id'));
        }

        return module_view('Admin', 'communities/index', [
            'ready' => true,
            'canManage' => $canManage,
            'communities' => $builder->get()->getResultArray(),
            'admins' => $canManage
                ? $db->table('tbl_admins')->where('status', 'active')->orderBy('name')->get()->getResultArray()
                : [],
        ]);
    }

    public function store()
    {
        return $this->save();
    }

    public function update(int $communityId)
    {
        return $this->save($communityId);
    }

    public function delete(int $communityId)
    {
        $this->requireSuperadmin();
        if (! $this->isReady()) {
            return redirect()->to(site_url('admin/communities'))->with('error', 'Run the Communities migration before managing communities.');
        }

        $db = db_connect();
        $db->table(self::MESSAGE_TABLE)->where('community_id', $communityId)->delete();
        $db->table(self::COMMUNITY_TABLE)->where('community_id', $communityId)->delete();

        return redirect()->to(site_url('admin/communities'))->with('success', 'Country community deleted.');
    }

    public function chats()
    {
        if (! $this->isReady()) {
            return module_view('Admin', 'communities/chats', ['ready' => false]);
        }

        $communities = $this->accessibleCommunities();
        $communityId = (int) $this->request->getGet('community');
        if ($communityId === 0 && $communities) {
            $communityId = (int) $communities[0]['community_id'];
        }
        if (! $this->findAccessibleCommunity($communityId)) {
            $communityId = 0;
        }

        $threads = $communityId ? $this->threads($communityId) : [];
        $attendeeId = (int) $this->request->getGet('attendee');
        if ($attendeeId > 0 && ! array_key_exists($attendeeId, $threads)) {
            $attendeeId = 0;
        }

        return module_view('Admin', 'communities/chats', [
            'ready' => true,
            'communities' => $communities,
            'selectedCommunityId' => $communityId,
            'threads' => $threads,
            'selectedAttendeeId' => $attendeeId,
        ]);
    }

    public function chatMessages(int $communityId, int $attendeeId)
    {
        if (! $this->findAccessibleCommunity($communityId)) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'You do not manage this community.']);
        }

        $db = db_connect();
        $messages = $db->table(self::MESSAGE_TABLE)
            ->where('community_id', $communityId)
            ->where('attendee_id', $attendeeId)
            ->orderBy('created_at', 'ASC')
            ->get()->getResultArray();

        $db->table(self::MESSAGE_TABLE)
            ->where('community_id', $communityId)
            ->where('attendee_id', $attendeeId)
            ->where('sender_type', 'attendee')
            ->where('read_at IS NULL', null, false)
            ->update(['read_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['data' => $messages]);
    }

    public function sendChat(int $communityId, int $attendeeId)
    {
        if (! $this->findAccessibleCommunity($communityId)) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'You do not manage this community.']);
        }

        $message = trim((string) $this->request->getPost('message'));
        if ($message === '' || mb_strlen($message) > 2000) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Enter a message of up to 2,000 characters.']);
        }

        db_connect()->table(self::MESSAGE_TABLE)->insert([
            'community_id' => $communityId,
            'attendee_id' => $attendeeId,
            'sender_type' => 'admin',
            'sender_admin_id' => (int) session('admin_id'),
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    private function save(?int $communityId = null)
    {
        $this->requireSuperadmin();
        if (! $this->isReady()) {
            return redirect()->to(site_url('admin/communities'))->with('error', 'Run the Communities migration before managing communities.');
        }

        $db = db_connect();
        $existing = $communityId
            ? $db->table(self::COMMUNITY_TABLE)->where('community_id', $communityId)->get()->getRowArray()
            : null;
        if ($communityId && ! $existing) {
            throw PageNotFoundException::forPageNotFound();
        }

        $country = trim((string) $this->request->getPost('country'));
        $managerId = (int) $this->request->getPost('manager_admin_id');
        $whatsappUrl = trim((string) $this->request->getPost('whatsapp_url'));
        if ($country === '' || mb_strlen($country) > 120 || ! $managerId) {
            return redirect()->back()->withInput()->with('error', 'Choose a country and its active Country Manager.');
        }
        if ($whatsappUrl !== '' && ! filter_var($whatsappUrl, FILTER_VALIDATE_URL)) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid WhatsApp community link.');
        }
        if (! $db->table('tbl_admins')->where('id', $managerId)->where('status', 'active')->countAllResults()) {
            return redirect()->back()->withInput()->with('error', 'Choose an active administrator as Country Manager.');
        }

        $countryMatch = $db->table(self::COMMUNITY_TABLE)
            ->where('country', $country)
            ->get()->getRowArray();
        if ($countryMatch && (int) $countryMatch['community_id'] !== (int) $communityId) {
            return redirect()->back()->withInput()->with('error', 'A community for this country already exists.');
        }

        try {
            $managerPhoto = $this->saveImage('manager_photo', 'uploads/community_managers/', $existing['manager_photo'] ?? '');
            $whatsappQr = $this->saveImage('whatsapp_qr', 'uploads/community_qr/', $existing['whatsapp_qr_path'] ?? '');
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        if ($managerPhoto === '') {
            return redirect()->back()->withInput()->with('error', 'Upload a photo for the Country Manager.');
        }
        if ($whatsappUrl === '' && $whatsappQr === '') {
            return redirect()->back()->withInput()->with('error', 'Add a WhatsApp community link or upload its QR code.');
        }

        $data = [
            'country' => $country,
            'manager_admin_id' => $managerId,
            'manager_title' => mb_substr(trim((string) $this->request->getPost('manager_title')), 0, 255),
            'manager_photo' => $managerPhoto ?: null,
            'whatsapp_url' => $whatsappUrl ?: null,
            'whatsapp_qr_path' => $whatsappQr ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($existing) {
            $db->table(self::COMMUNITY_TABLE)->where('community_id', $communityId)->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table(self::COMMUNITY_TABLE)->insert($data);
        }

        return redirect()->to(site_url('admin/communities'))->with('success', 'Country community saved.');
    }

    private function saveImage(string $field, string $directory, string $existingPath): string
    {
        $file = $this->request->getFile($field);
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }
        if (! $file->isValid() || $file->hasMoved()) {
            throw new \RuntimeException('The image upload failed.');
        }
        if (! in_array(strtolower((string) $file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new \RuntimeException('Upload a JPG, PNG, or WebP image.');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \RuntimeException('Images must be 5 MB or smaller.');
        }

        $absoluteDirectory = FCPATH . $directory;
        if (! is_dir($absoluteDirectory) && ! mkdir($absoluteDirectory, 0755, true) && ! is_dir($absoluteDirectory)) {
            throw new \RuntimeException('Unable to prepare the image upload folder.');
        }
        $name = $file->getRandomName();
        $file->move($absoluteDirectory, $name);

        return $directory . $name;
    }

    private function accessibleCommunities(): array
    {
        $builder = db_connect()->table(self::COMMUNITY_TABLE . ' c')
            ->select('c.*, a.name AS manager_name')
            ->join('tbl_admins a', 'a.id = c.manager_admin_id', 'left')
            ->orderBy('c.sort_order')
            ->orderBy('c.country');
        if (! $this->isSuperadmin()) {
            $builder->where('c.manager_admin_id', (int) session('admin_id'));
        }

        return $builder->get()->getResultArray();
    }

    private function findAccessibleCommunity(int $communityId): ?array
    {
        if (! $this->isReady() || $communityId <= 0) {
            return null;
        }
        $builder = db_connect()->table(self::COMMUNITY_TABLE)->where('community_id', $communityId);
        if (! $this->isSuperadmin()) {
            $builder->where('manager_admin_id', (int) session('admin_id'));
        }

        return $builder->get()->getRowArray();
    }

    private function threads(int $communityId): array
    {
        $rows = db_connect()->table(self::MESSAGE_TABLE . ' m')
            ->select("m.attendee_id, m.message, m.created_at, m.sender_type, m.read_at, a.firstname, a.lastname, a.useremail")
            ->join('tbl_attendees a', 'a.attendee_id = m.attendee_id', 'left')
            ->where('m.community_id', $communityId)
            ->orderBy('m.created_at', 'DESC')
            ->get()->getResultArray();

        $threads = [];
        foreach ($rows as $row) {
            $attendeeId = (int) $row['attendee_id'];
            if (! isset($threads[$attendeeId])) {
                $name = trim((string) ($row['firstname'] ?? '') . ' ' . (string) ($row['lastname'] ?? ''));
                $threads[$attendeeId] = [
                    'attendee_id' => $attendeeId,
                    'name' => $name ?: 'Attendee',
                    'email' => (string) ($row['useremail'] ?? ''),
                    'last_message' => (string) $row['message'],
                    'last_message_at' => (string) $row['created_at'],
                    'unread' => 0,
                ];
            }
            if ($row['sender_type'] === 'attendee' && empty($row['read_at'])) {
                $threads[$attendeeId]['unread']++;
            }
        }

        return $threads;
    }

    private function requireSuperadmin(): void
    {
        if (! $this->isSuperadmin()) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    private function isSuperadmin(): bool
    {
        return strtolower((string) preg_replace('/[\s_-]+/', '', (string) session('admin_role'))) === 'superadmin';
    }

    private function isReady(): bool
    {
        $db = db_connect();

        return $db->tableExists(self::COMMUNITY_TABLE)
            && $db->tableExists(self::MESSAGE_TABLE);
    }
}
