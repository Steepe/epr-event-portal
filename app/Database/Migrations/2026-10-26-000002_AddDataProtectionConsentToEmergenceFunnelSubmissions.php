<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDataProtectionConsentToEmergenceFunnelSubmissions extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('data_protection_consent', 'tbl_emergence_funnel_submissions')) {
            $this->forge->addColumn('tbl_emergence_funnel_submissions', [
                'data_protection_consent' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'referral_source',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('data_protection_consent', 'tbl_emergence_funnel_submissions')) {
            $this->forge->dropColumn('tbl_emergence_funnel_submissions', 'data_protection_consent');
        }
    }
}
