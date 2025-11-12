<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:03
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class ConferenceModel extends Model
{
    protected string $table = 'tbl_conferences';
    protected string $primaryKey = 'conference_id';

    protected array $allowedFields = [
        'title',
        'year',
        'status',
        'icon',
        'description',
        'start_date',
        'end_date',
        'location',
        'created_at',
        'updated_at'
    ];

    protected bool $useTimestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
}
