<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePortalUserPresence extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_portal_user_presence')) {
            return;
        }

        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'last_seen_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->addKey('last_seen_at');
        $this->forge->createTable('tbl_portal_user_presence', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_portal_user_presence', true);
    }
}
