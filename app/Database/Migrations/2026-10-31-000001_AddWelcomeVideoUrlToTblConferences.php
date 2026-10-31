<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWelcomeVideoUrlToTblConferences extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('welcome_video_url', 'tbl_conferences')) {
            return;
        }

        $this->forge->addColumn('tbl_conferences', [
            'welcome_video_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 2048,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('welcome_video_url', 'tbl_conferences')) {
            $this->forge->dropColumn('tbl_conferences', 'welcome_video_url');
        }
    }
}
