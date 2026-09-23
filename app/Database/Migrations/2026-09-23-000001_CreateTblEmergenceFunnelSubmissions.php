<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTblEmergenceFunnelSubmissions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'referral_source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'marketing_consent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'utm_source' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'utm_medium' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'utm_campaign' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'db_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'mailchimp_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'mailchimp_detail' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('email');
        $this->forge->addKey('created_at');
        $this->forge->createTable('tbl_emergence_funnel_submissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_emergence_funnel_submissions', true);
    }
}
