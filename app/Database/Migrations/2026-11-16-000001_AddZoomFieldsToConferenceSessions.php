<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddZoomFieldsToConferenceSessions extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('livepush_id', 'tbl_conference_sessions')) {
            $fields['livepush_id'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'vimeo_id',
            ];
        }

        if (! $this->db->fieldExists('stream_provider', 'tbl_conference_sessions')) {
            $fields['stream_provider'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'livepush',
                'after'      => 'livepush_id',
            ];
        }

        if (! $this->db->fieldExists('zoom_meeting_number', 'tbl_conference_sessions')) {
            $fields['zoom_meeting_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
                'after'      => 'stream_provider',
            ];
        }

        if (! $this->db->fieldExists('zoom_passcode', 'tbl_conference_sessions')) {
            $fields['zoom_passcode'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'zoom_meeting_number',
            ];
        }

        if (! $this->db->fieldExists('zoom_is_webinar', 'tbl_conference_sessions')) {
            $fields['zoom_is_webinar'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'zoom_passcode',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('tbl_conference_sessions', $fields);
        }

        // Earlier Livepush sessions were temporarily stored in vimeo_id. Keep
        // historical Vimeo recordings untouched, but make their legacy value
        // available to the new live-session field before this code is used.
        if ($this->db->fieldExists('livepush_id', 'tbl_conference_sessions')
            && $this->db->fieldExists('vimeo_id', 'tbl_conference_sessions')) {
            $this->db->query(
                "UPDATE `tbl_conference_sessions`
                 SET `livepush_id` = `vimeo_id`
                 WHERE (`livepush_id` IS NULL OR `livepush_id` = '')
                   AND `vimeo_id` IS NOT NULL AND `vimeo_id` <> ''"
            );
        }
    }

    public function down()
    {
        foreach (['zoom_is_webinar', 'zoom_passcode', 'zoom_meeting_number', 'stream_provider', 'livepush_id'] as $field) {
            if ($this->db->fieldExists($field, 'tbl_conference_sessions')) {
                $this->forge->dropColumn('tbl_conference_sessions', $field);
            }
        }
    }
}
