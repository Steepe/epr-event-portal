<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class PresenceController extends BaseController
{
    private const TABLE = 'tbl_portal_user_presence';

    public function heartbeat()
    {
        if (! session('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
        }

        $userId = (int) session('user_id');
        $db = db_connect();
        if ($userId <= 0 || ! $db->tableExists(self::TABLE)) {
            return $this->response->setStatusCode(503)->setJSON(['status' => 'unavailable']);
        }

        $table = $db->table(self::TABLE);
        $existing = $table->where('user_id', $userId)->get()->getRowArray();
        if ($existing) {
            $table->where('user_id', $userId)->update(['last_seen_at' => date('Y-m-d H:i:s')]);
        } else {
            $table->insert(['user_id' => $userId, 'last_seen_at' => date('Y-m-d H:i:s')]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public static function clearForUser(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        try {
            $db = db_connect();
            if ($db->tableExists(self::TABLE)) {
                $db->table(self::TABLE)->where('user_id', $userId)->delete();
            }
        } catch (\Throwable $exception) {
            log_message('debug', '[PresenceController] Unable to clear attendee presence: ' . $exception->getMessage());
        }
    }
}
