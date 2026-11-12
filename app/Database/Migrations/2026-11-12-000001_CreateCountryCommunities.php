<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCountryCommunities extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('tbl_country_communities')) {
            $this->forge->addField([
                'community_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'country' => ['type' => 'VARCHAR', 'constraint' => 120],
                'manager_admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'manager_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'manager_photo' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'whatsapp_url' => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
                'whatsapp_qr_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('community_id', true);
            $this->forge->addUniqueKey('country');
            $this->forge->addKey(['is_active', 'sort_order']);
            $this->forge->addKey('manager_admin_id');
            $this->forge->createTable('tbl_country_communities', true);
        }

        if (! $this->db->tableExists('tbl_country_community_messages')) {
            $this->forge->addField([
                'community_message_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'community_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'attendee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'sender_type' => ['type' => 'VARCHAR', 'constraint' => 20],
                'sender_admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'message' => ['type' => 'TEXT'],
                'read_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('community_message_id', true);
            $this->forge->addKey(['community_id', 'attendee_id', 'created_at']);
            $this->forge->addKey(['attendee_id', 'created_at']);
            $this->forge->createTable('tbl_country_community_messages', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('tbl_country_community_messages', true);
        $this->forge->dropTable('tbl_country_communities', true);
    }
}
