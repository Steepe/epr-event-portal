<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSessionReplayFields extends Migration
{
    public function up()
    {
        $fields = [];
        if (! $this->db->fieldExists('replay_bunny_embed_url', 'tbl_conference_sessions')) {
            $fields['replay_bunny_embed_url'] = ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true];
        }
        if (! $this->db->fieldExists('replay_product_id', 'tbl_conference_sessions')) {
            $fields['replay_product_id'] = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true];
        }
        if ($fields !== []) {
            $this->forge->addColumn('tbl_conference_sessions', $fields);
        }
    }

    public function down()
    {
        foreach (['replay_product_id', 'replay_bunny_embed_url'] as $field) {
            if ($this->db->fieldExists($field, 'tbl_conference_sessions')) {
                $this->forge->dropColumn('tbl_conference_sessions', $field);
            }
        }
    }
}
