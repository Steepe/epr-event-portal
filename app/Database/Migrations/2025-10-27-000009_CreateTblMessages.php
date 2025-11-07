<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:53
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMessages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'message_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'sender_id' => ['type' => 'INT', 'unsigned' => true],
            'receiver_id' => ['type' => 'INT', 'unsigned' => true],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'body' => ['type' => 'TEXT'],
            'is_read' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('message_id', true);
        $this->forge->addForeignKey('sender_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_messages', true);
    }
}
