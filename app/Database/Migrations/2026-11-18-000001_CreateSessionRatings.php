<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSessionRatings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_session_ratings')) {
            return;
        }

        $this->forge->addField([
            'rating_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sessions_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            // attendee_id stores the canonical tbl_users.id value used throughout the portal.
            'attendee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'rating' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('rating_id', true);
        $this->forge->addKey(['sessions_id', 'attendee_id'], false, true);
        $this->forge->createTable('tbl_session_ratings', true);
    }

    public function down()
    {
        if ($this->db->tableExists('tbl_session_ratings')) {
            $this->forge->dropTable('tbl_session_ratings', true);
        }
    }
}
