<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:51
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblSessions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'session_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'event_id' => ['type' => 'INT', 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'start_time' => ['type' => 'DATETIME', 'null' => true],
            'end_time' => ['type' => 'DATETIME', 'null' => true],
            'video_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'zoom_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'chat_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'is_zoom' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('session_id', true);
        $this->forge->addForeignKey('event_id', 'tbl_events', 'event_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_sessions', true);
    }
}
