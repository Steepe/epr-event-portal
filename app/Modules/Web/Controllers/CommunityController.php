<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class CommunityController extends BaseController
{
    private const COMMUNITY_TABLE = 'tbl_country_communities';
    private const MESSAGE_TABLE = 'tbl_country_community_messages';

    public function index(): string
    {
        if (! $this->isReady()) {
            return module_view('Web', 'communities', ['ready' => false, 'communities' => []]);
        }

        $communities = db_connect()->table(self::COMMUNITY_TABLE . ' c')
            ->select('c.*, a.name AS manager_name')
            ->join('tbl_admins a', 'a.id = c.manager_admin_id', 'left')
            ->where('c.is_active', 1)
            ->orderBy('c.sort_order')
            ->orderBy('c.country')
            ->get()->getResultArray();

        return module_view('Web', 'communities', [
            'ready' => true,
            'communities' => $communities,
        ]);
    }

    public function chatMessages(int $communityId)
    {
        $community = $this->activeCommunity($communityId);
        if (! $community) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Community not found.']);
        }

        $attendeeId = $this->attendeeId();
        $db = db_connect();
        $messages = $db->table(self::MESSAGE_TABLE)
            ->where('community_id', $communityId)
            ->where('attendee_id', $attendeeId)
            ->orderBy('created_at', 'ASC')
            ->get()->getResultArray();

        $db->table(self::MESSAGE_TABLE)
            ->where('community_id', $communityId)
            ->where('attendee_id', $attendeeId)
            ->where('sender_type', 'admin')
            ->where('read_at IS NULL', null, false)
            ->update(['read_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['data' => $messages]);
    }

    public function sendChat(int $communityId)
    {
        if (! $this->activeCommunity($communityId)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Community not found.']);
        }

        $message = trim((string) $this->request->getPost('message'));
        if ($message === '' || mb_strlen($message) > 2000) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Enter a message of up to 2,000 characters.']);
        }

        db_connect()->table(self::MESSAGE_TABLE)->insert([
            'community_id' => $communityId,
            'attendee_id' => $this->attendeeId(),
            'sender_type' => 'attendee',
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    private function activeCommunity(int $communityId): ?array
    {
        if (! $this->isReady()) {
            return null;
        }

        return db_connect()->table(self::COMMUNITY_TABLE)
            ->where('community_id', $communityId)
            ->where('is_active', 1)
            ->get()->getRowArray();
    }

    private function attendeeId(): int
    {
        return (int) (session('attendee_id') ?: session('user_id'));
    }

    private function isReady(): bool
    {
        $db = db_connect();

        return $db->tableExists(self::COMMUNITY_TABLE)
            && $db->tableExists(self::MESSAGE_TABLE);
    }
}
