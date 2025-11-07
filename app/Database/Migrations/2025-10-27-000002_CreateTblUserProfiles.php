<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:48
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblUserProfiles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'profile_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'firstname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'lastname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'telephone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'company' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'city' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'state' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'country' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'profile_picture' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'social_links' => ['type' => 'TEXT', 'null' => true],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('profile_id', true);
        $this->forge->addForeignKey('user_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_user_profiles', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_user_profiles', true);
    }
}
