<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:29
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTblUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'uuid'          => ['type' => 'CHAR', 'constraint' => 36],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'          => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'attendee', 'speaker', 'sponsor', 'exhibitor'],
                'default'    => 'attendee'
            ],
            'is_verified'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('tbl_users', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_users', true);
    }
}
