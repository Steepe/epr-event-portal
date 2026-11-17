<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLinkedinUrlToSpeakers extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('linkedin_url', 'tbl_speakers')) {
            $this->forge->addColumn('tbl_speakers', [
                'linkedin_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'speaker_photo'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('linkedin_url', 'tbl_speakers')) {
            $this->forge->dropColumn('tbl_speakers', 'linkedin_url');
        }
    }
}
