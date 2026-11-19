<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowAdminSponsorTiers extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('tier', 'tbl_sponsors')) {
            return;
        }

        // The legacy column is an ENUM limited to Diamond, Platinum and the
        // misspelled "Distruptor". Admin now offers Gold, Silver and Bronze.
        // VARCHAR preserves legacy values while the controller validates all
        // newly saved categories.
        $this->db->query(
            "ALTER TABLE `tbl_sponsors` MODIFY `tier` VARCHAR(50) NOT NULL DEFAULT 'Platinum'"
        );
    }

    public function down()
    {
        throw new \RuntimeException(
            'This migration cannot be safely rolled back after Gold, Silver, or Bronze sponsors have been saved.'
        );
    }
}
