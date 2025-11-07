<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:50
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblUserTokens extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'token_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'token' => ['type' => 'VARCHAR', 'constraint' => 512],
            'type' => ['type' => 'ENUM', 'constraint' => ['api', 'sso', 'reset'], 'default' => 'api'],
            'expires_at' => ['type' => 'DATETIME', 'null' => true],
            'meta' => ['type' => 'TEXT', 'null' => true],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('token_id', true);
        $this->forge->addUniqueKey('token');
        $this->forge->addForeignKey('user_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_user_tokens', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_user_tokens', true);
    }
}
