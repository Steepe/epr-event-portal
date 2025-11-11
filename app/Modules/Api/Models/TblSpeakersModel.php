<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 20:42
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblSpeakersModel extends Model
{
    protected string $table         = 'tbl_speakers';   // ✅ correct table
    protected string $primaryKey    = 'speaker_id';
    protected string $returnType    = 'array';

    protected array $allowedFields  = [
        'speaker_name',
        'speaker_title',
        'speaker_company',
        'speaker_photo',
        'bio'
    ];
}
