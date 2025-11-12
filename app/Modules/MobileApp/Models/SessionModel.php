<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:41
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class SessionModel extends Model
{
    protected string $table = 'tbl_conference_sessions';
    protected string $primaryKey = 'sessions_id';

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
        'tags'
    ];

    protected bool $useTimestamps = false;
}
