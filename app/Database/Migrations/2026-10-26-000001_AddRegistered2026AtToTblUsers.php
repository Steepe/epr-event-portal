<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegistered2026AtToTblUsers extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('registered_2026_at', 'tbl_users')) {
            return;
        }

        $this->forge->addColumn('tbl_users', [
            'registered_2026_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('registered_2026_at', 'tbl_users')) {
            $this->forge->dropColumn('tbl_users', 'registered_2026_at');
        }
    }
}
