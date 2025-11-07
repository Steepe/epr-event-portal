<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:49
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblUserIntegrations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'integration_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'provider' => ['type' => 'VARCHAR', 'constraint' => 50],
            'external_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'access_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'token_expiry' => ['type' => 'DATETIME', 'null' => true],
            'meta' => ['type' => 'TEXT', 'null' => true],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('integration_id', true);
        $this->forge->addForeignKey('user_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_user_integrations', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_user_integrations', true);
    }
}
