<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminActivityLogs extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_admin_activity_logs')) {
            return;
        }

        $this->forge->addField([
            'activity_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'admin_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'admin_email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'event' => ['type' => 'VARCHAR', 'constraint' => 100],
            'http_method' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'route' => ['type' => 'VARCHAR', 'constraint' => 2048],
            'resource_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'resource_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'details' => ['type' => 'TEXT', 'null' => true],
            'response_status' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('activity_id', true);
        $this->forge->addKey(['admin_id', 'created_at']);
        $this->forge->addKey(['event', 'created_at']);
        $this->forge->createTable('tbl_admin_activity_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_admin_activity_logs', true);
    }
}
