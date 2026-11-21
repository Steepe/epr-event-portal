<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Throwable;

/** Records a logged-in attendee opening a conference-session page. */
class SessionViewerTracker
{
    private const TABLE = 'tbl_session_viewers';

    public function __construct(private BaseConnection $db)
    {
    }

    public function record(int $sessionId, int $attendeeId): void
    {
        // Deployments remain usable until the deliberately-run migration creates this table.
        if ($sessionId <= 0 || $attendeeId <= 0 || ! $this->db->tableExists(self::TABLE)) {
            return;
        }

        try {
            $table = $this->db->table(self::TABLE);
            $existing = $table->select('viewer_id, view_count')
                ->where('sessions_id', $sessionId)
                ->where('attendee_id', $attendeeId)
                ->get()
                ->getRowArray();
            $now = date('Y-m-d H:i:s');

            if ($existing) {
                $table->where('viewer_id', (int) $existing['viewer_id'])->update([
                    'last_viewed_at' => $now,
                    'view_count' => ((int) $existing['view_count']) + 1,
                ]);
                return;
            }

            $table->insert([
                'sessions_id' => $sessionId,
                'attendee_id' => $attendeeId,
                'first_viewed_at' => $now,
                'last_viewed_at' => $now,
                'view_count' => 1,
            ]);
        } catch (Throwable $error) {
            // Viewer analytics must never stop an attendee from entering a session.
            log_message('error', 'Unable to record session viewer ({type}).', ['type' => get_class($error)]);
        }
    }
}
