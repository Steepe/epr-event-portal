<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmergenceBoothTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('tbl_emergence_booth_settings')) {
            $this->forge->addField([
                'setting_key' => ['type' => 'VARCHAR', 'constraint' => 100],
                'setting_value' => ['type' => 'TEXT', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('setting_key', true);
            $this->forge->createTable('tbl_emergence_booth_settings', true);
        }

        if (! $this->db->tableExists('tbl_emergence_booth_members')) {
            $this->forge->addField([
                'booth_member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'display_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('booth_member_id', true);
            $this->forge->addUniqueKey('admin_id');
            $this->forge->addKey(['is_active', 'sort_order']);
            $this->forge->createTable('tbl_emergence_booth_members', true);
        }

        if (! $this->db->tableExists('tbl_admin_presence')) {
            $this->forge->addField([
                'admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'last_seen_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('admin_id', true);
            $this->forge->addKey('last_seen_at');
            $this->forge->createTable('tbl_admin_presence', true);
        }

        if (! $this->db->tableExists('tbl_emergence_booth_programmes')) {
            $this->forge->addField([
                'programme_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255],
                'label' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'description' => ['type' => 'TEXT', 'null' => true],
                'icon' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'fa-rocket'],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('programme_id', true);
            $this->forge->addKey(['is_active', 'sort_order']);
            $this->forge->createTable('tbl_emergence_booth_programmes', true);
        }

        if (! $this->db->tableExists('tbl_emergence_booth_resources')) {
            $this->forge->addField([
                'resource_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255],
                'meta' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'file_path' => ['type' => 'VARCHAR', 'constraint' => 500],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('resource_id', true);
            $this->forge->addKey(['is_active', 'sort_order']);
            $this->forge->createTable('tbl_emergence_booth_resources', true);
        }

        if (! $this->db->tableExists('tbl_emergence_booth_callback_requests')) {
            $this->forge->addField([
                'callback_request_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'attendee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'topic' => ['type' => 'VARCHAR', 'constraint' => 100],
                'note' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'new'],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('callback_request_id', true);
            $this->forge->addKey(['status', 'created_at']);
            $this->forge->addKey('attendee_id');
            $this->forge->createTable('tbl_emergence_booth_callback_requests', true);
        }

        if (! $this->db->tableExists('tbl_emergence_booth_messages')) {
            $this->forge->addField([
                'booth_message_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'booth_member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'attendee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'sender_type' => ['type' => 'VARCHAR', 'constraint' => 20],
                'sender_admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'message' => ['type' => 'TEXT'],
                'read_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('booth_message_id', true);
            $this->forge->addKey(['booth_member_id', 'attendee_id', 'created_at']);
            $this->forge->addKey(['attendee_id', 'created_at']);
            $this->forge->createTable('tbl_emergence_booth_messages', true);
        }
    }

    public function down()
    {
        foreach ([
            'tbl_emergence_booth_messages',
            'tbl_emergence_booth_callback_requests',
            'tbl_emergence_booth_resources',
            'tbl_emergence_booth_programmes',
            'tbl_admin_presence',
            'tbl_emergence_booth_members',
            'tbl_emergence_booth_settings',
        ] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
