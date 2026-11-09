<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminPortalMessages extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('tbl_admin_portal_messages')) {
            $this->forge->addField([
                'admin_message_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'sender_admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'subject' => ['type' => 'VARCHAR', 'constraint' => 255],
                'body' => ['type' => 'TEXT'],
                'recipient_type' => ['type' => 'VARCHAR', 'constraint' => 30],
                'recipient_filters' => ['type' => 'TEXT', 'null' => true],
                'recipient_count' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('admin_message_id', true);
            $this->forge->addKey(['sender_admin_id', 'created_at']);
            $this->forge->createTable('tbl_admin_portal_messages', true);
        }

        if (! $this->db->tableExists('tbl_admin_portal_message_deliveries')) {
            $this->forge->addField([
                'delivery_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'admin_message_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
                'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'email' => ['type' => 'VARCHAR', 'constraint' => 254],
                'email_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
                'email_error' => ['type' => 'TEXT', 'null' => true],
                'sent_at' => ['type' => 'DATETIME', 'null' => true],
                'read_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('delivery_id', true);
            $this->forge->addKey(['admin_message_id', 'created_at']);
            $this->forge->addKey(['user_id', 'read_at']);
            $this->forge->addUniqueKey(['admin_message_id', 'user_id']);
            $this->forge->createTable('tbl_admin_portal_message_deliveries', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('tbl_admin_portal_message_deliveries', true);
        $this->forge->dropTable('tbl_admin_portal_messages', true);
    }
}
