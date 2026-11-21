<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSessionViewerAnalytics extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_session_viewers')) {
            return;
        }

        $this->forge->addField([
            'viewer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            // The current portal's session primary key is tbl_conference_sessions.sessions_id.
            'sessions_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            // Canonical attendee account ID (tbl_users.id).
            'attendee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'first_viewed_at' => ['type' => 'DATETIME'],
            'last_viewed_at' => ['type' => 'DATETIME'],
            // Kept for diagnostics only; analytics uses the unique attendee/session rows.
            'view_count' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
        ]);
        $this->forge->addKey('viewer_id', true);
        $this->forge->addKey(['sessions_id', 'attendee_id'], false, true);
        $this->forge->addKey('sessions_id');
        $this->forge->createTable('tbl_session_viewers', true);
    }

    public function down()
    {
        if ($this->db->tableExists('tbl_session_viewers')) {
            $this->forge->dropTable('tbl_session_viewers', true);
        }
    }
}
