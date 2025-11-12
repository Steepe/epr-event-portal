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

class SpeakerModel extends Model
{
    protected string $table = 'tbl_session_speakers';
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'session_id', 'speaker_id'
    ];
}
