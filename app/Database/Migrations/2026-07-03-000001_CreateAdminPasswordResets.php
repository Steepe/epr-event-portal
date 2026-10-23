<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminPasswordResets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'admin_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 190,
            ],
            'token_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('admin_id');
        $this->forge->addKey('email');
        $this->forge->addUniqueKey('token_hash');
        $this->forge->createTable('admin_password_resets', true);
    }

    public function down()
    {
        $this->forge->dropTable('admin_password_resets', true);
    }
}
