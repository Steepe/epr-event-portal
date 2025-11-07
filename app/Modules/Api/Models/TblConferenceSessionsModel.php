<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 05:27
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblConferenceSessionsModel extends Model
{
    protected string $table = 'tbl_conference_sessions';
    protected string $primaryKey = 'sessions_id';
    protected array $allowedFields = [
        'conference_id', 'sessions_name', 'event_date', 'start_time', 'end_time',
        'access_level', 'description', 'is_zoom', 'zoom_link', 'vimeo_id',
        'workbook', 'tags', 'tags_meta'
    ];

    protected bool $useTimestamps = false;

    public function getSessionsWithRelations($conferenceId): array
    {
        $builder = $this->db->table($this->table . ' as s');
        $builder->select('s.*, sp.speaker_name, sp.speaker_title, sp.speaker_company, sp.speaker_photo,
                          sr.sponsor_name, sr.sponsor_logo, sr.sponsor_link');
        $builder->join('tbl_session_speakers as sp', 'sp.sessions_id = s.sessions_id', 'left');
        $builder->join('tbl_session_sponsors as sr', 'sr.sessions_id = s.sessions_id', 'left');
        $builder->where('s.conference_id', $conferenceId);
        $builder->orderBy('s.event_date, s.start_time', 'ASC');
        $query = $builder->get();

        $result = [];
        foreach ($query->getResultArray() as $row) {
            $sid = $row['sessions_id'];
            if (!isset($result[$sid])) {
                $result[$sid] = [
                    'sessions_id' => $row['sessions_id'],
                    'sessions_name' => $row['sessions_name'],
                    'event_date' => $row['event_date'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'access_level' => $row['access_level'],
                    'description' => $row['description'],
                    'is_zoom' => $row['is_zoom'],
                    'zoom_link' => $row['zoom_link'],
                    'vimeo_id' => $row['vimeo_id'],
                    'workbook' => $row['workbook'],
                    'tags' => $row['tags'],
                    'tags_meta' => $row['tags_meta'],
                    'speakers' => [],
                    'sponsors' => []
                ];
            }

            if ($row['speaker_name']) {
                $result[$sid]['speakers'][] = [
                    'speaker_name' => $row['speaker_name'],
                    'speaker_title' => $row['speaker_title'],
                    'speaker_company' => $row['speaker_company'],
                    'speaker_photo' => $row['speaker_photo'],
                ];
            }

            if ($row['sponsor_name']) {
                $result[$sid]['sponsors'][] = [
                    'sponsor_name' => $row['sponsor_name'],
                    'sponsor_logo' => $row['sponsor_logo'],
                    'sponsor_link' => $row['sponsor_link'],
                ];
            }
        }

        return array_values($result);
    }
}
