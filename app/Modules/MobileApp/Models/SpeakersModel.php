<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:59
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class SpeakersModel extends Model
{
    protected string $table = 'tbl_speakers';
    protected string $primaryKey = 'speaker_id';
    protected array $allowedFields = [
        'speaker_name', 'speaker_title', 'speaker_company',
        'speaker_photo', 'bio', 'linkedin_url', 'speaker_email', 'created_at', 'updated_at'
    ];

    public function getAllSpeakers(?int $conferenceId = null): array
    {
        $builder = $this->builder();
        $builder->orderBy('speaker_name', 'ASC');
        $speakers = $builder->get()->getResultArray();

        foreach ($speakers as &$speaker) {
            $speaker['sessions'] = $this->getSpeakerSessions((int) $speaker['speaker_id'], $conferenceId);
        }

        return $speakers;
    }

    protected function getSpeakerSessions(int $speakerId, ?int $conferenceId = null): array
    {
        $db = \Config\Database::connect();
        $query = $db->table('tbl_session_speakers as ss')
            ->select('s.sessions_id, s.sessions_name')
            ->join('tbl_conference_sessions as s', 's.sessions_id = ss.sessions_id', 'left')
            ->where('ss.speaker_id', $speakerId);
        if ($conferenceId !== null && $conferenceId > 0) {
            $query->where('s.conference_id', $conferenceId);
        }

        return $query->orderBy('s.event_date', 'ASC')->orderBy('s.start_time', 'ASC')->get()->getResultArray();
    }

    public function getSpeakersBySession($sessionId)
    {
        $db = \Config\Database::connect();

        $sql = "
        SELECT sp.speaker_id, sp.speaker_name, sp.speaker_title,
               sp.speaker_company, sp.speaker_photo, sp.bio, sp.linkedin_url
        FROM tbl_session_speakers AS ss
        LEFT JOIN tbl_speakers AS sp ON sp.speaker_id = ss.speaker_id
        WHERE ss.sessions_id = ?
        ORDER BY sp.speaker_name ASC
    ";

        $query = $db->query($sql, [$sessionId]);
        return $query->getResultArray();
    }

}
