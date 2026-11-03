<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblSpeakerOffers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_speaker_offers')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'speaker_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'cta_link' => [
                'type'       => 'VARCHAR',
                'constraint' => 2048,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('speaker_id');
        $this->forge->createTable('tbl_speaker_offers', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_speaker_offers', true);
    }
}
