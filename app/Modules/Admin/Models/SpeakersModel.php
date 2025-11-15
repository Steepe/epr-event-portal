<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 06:27
 */

namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class SpeakersModel extends Model
{
    protected string $table = 'tbl_speakers';
    protected string $primaryKey = 'speaker_id';

    protected array $allowedFields = [
        'speaker_name',
        'speaker_title',
        'speaker_email',
        'speaker_company',
        'bio',
        'speaker_photo'
    ];

    protected bool $useTimestamps = false;
}
