<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:52
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblAttendeeSessions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'session_id' => ['type' => 'INT', 'unsigned' => true],
            'joined_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('session_id', 'tbl_sessions', 'session_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_attendee_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_attendee_sessions', true);
    }
}
