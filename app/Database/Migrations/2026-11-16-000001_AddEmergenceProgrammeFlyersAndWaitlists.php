<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmergenceProgrammeFlyersAndWaitlists extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_emergence_booth_programmes')) {
            if (! $this->db->fieldExists('flyer_path', 'tbl_emergence_booth_programmes')) {
                $this->forge->addColumn('tbl_emergence_booth_programmes', [
                    'flyer_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'icon'],
                    'flyer_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'flyer_path'],
                ]);
            }
            if (! $this->db->fieldExists('waitlist_enabled', 'tbl_emergence_booth_programmes')) {
                $this->forge->addColumn('tbl_emergence_booth_programmes', [
                    'waitlist_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'is_active'],
                ]);
            }
        }

        if (! $this->db->tableExists('tbl_emergence_booth_programme_waitlists')) {
            $this->forge->addField([
                'programme_waitlist_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'programme_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'attendee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('programme_waitlist_id', true);
            $this->forge->addKey(['programme_id', 'created_at']);
            $this->forge->addKey('attendee_id');
            $this->forge->addUniqueKey(['programme_id', 'attendee_id']);
            $this->forge->createTable('tbl_emergence_booth_programme_waitlists', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tbl_emergence_booth_programme_waitlists')) {
            $this->forge->dropTable('tbl_emergence_booth_programme_waitlists', true);
        }

        if ($this->db->tableExists('tbl_emergence_booth_programmes')) {
            foreach (['flyer_name', 'flyer_path', 'waitlist_enabled'] as $field) {
                if ($this->db->fieldExists($field, 'tbl_emergence_booth_programmes')) {
                    $this->forge->dropColumn('tbl_emergence_booth_programmes', $field);
                }
            }
        }
    }
}
