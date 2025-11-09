<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 04:34
 */

namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class ConferenceSessionsModel extends Model
{
    protected string $table = 'tbl_conference_sessions';
    protected string $primaryKey = 'sessions_id';
    protected string $returnType = 'array';
    protected bool $useTimestamps = false;

    protected array $allowedFields = [
        'conference_id',
        'sessions_name',
        'event_date',
        'start_time',
        'end_time',
        'access_level',
        'description',
        'vimeo_id',
        'workbook',
        'tags',
    ];
}
