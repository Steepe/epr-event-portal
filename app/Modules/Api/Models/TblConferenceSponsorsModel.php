<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 05:24
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblConferenceSponsorsModel extends Model
{
    protected string $table = 'tbl_conference_sponsors';
    protected string $primaryKey = 'id';
    protected array $allowedFields = ['conference_id', 'sponsor_name', 'sponsor_logo', 'sponsor_link'];
    protected bool $useTimestamps = false;

    public function getSponsorsByConference($conferenceId): array
    {
        return $this->where('conference_id', $conferenceId)->findAll();
    }
}
