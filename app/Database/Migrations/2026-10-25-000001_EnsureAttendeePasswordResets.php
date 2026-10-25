<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsureAttendeePasswordResets extends Migration
{
    public function up()
    {
        $this->db->resetDataCache();
        $timestamps = [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'expires_at' => ['type' => 'DATETIME', 'null' => true],
        ];
        if (!$this->db->tableExists('password_resets')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'email' => ['type' => 'VARCHAR', 'constraint' => 190],
                'token' => ['type' => 'VARCHAR', 'constraint' => 64],
            ] + $timestamps);
            $this->forge->addKey('id', true);
            $this->forge->addKey('email');
            $this->forge->addUniqueKey('token');
            $this->forge->createTable('password_resets', true);
            return;
        }
        foreach ($timestamps as $name => $definition) {
            if (!$this->db->fieldExists($name, 'password_resets')) {
                $this->forge->addColumn('password_resets', [$name => $definition]);
                $this->db->resetDataCache();
            }
        }
    }

    public function down()
    {
        // Preserve the pre-existing table and outstanding reset links on rollback.
    }
}
