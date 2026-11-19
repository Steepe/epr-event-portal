<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblAttendeeConnections extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'requester_id' => ['type' => 'INT', 'unsigned' => true],
            'receiver_id' => ['type' => 'INT', 'unsigned' => true],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'declined'],
                'default' => 'pending',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'accepted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('requester_id');
        $this->forge->addKey('receiver_id');
        $this->forge->addUniqueKey(['requester_id', 'receiver_id'], 'uniq_attendee_connection_pair');
        $this->forge->addForeignKey('requester_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_attendee_connections', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_attendee_connections', true);
    }
}
