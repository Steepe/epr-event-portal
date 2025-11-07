<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 06:33
 */

/**
 * TblSessionSpeakersModel
 * Handles CRUD operations for session speakers.
 *
 * Author: Oluwamayowa Steepe
 * Project: epr-event-portal
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblSessionSpeakersModel extends Model
{
    protected string $table         = 'tbl_session_speakers';
    protected string $primaryKey    = 'id';
    protected string $returnType    = 'array';
    protected bool $useSoftDeletes = false;

    protected array $allowedFields = [
        'sessions_id',
        'speaker_name',
        'speaker_title',
        'speaker_company',
        'speaker_photo'
    ];

    protected bool $useTimestamps = false;
}
