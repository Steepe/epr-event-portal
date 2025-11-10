<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 00:26
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class ConferenceModel extends Model
{
    protected string $table = 'tbl_conferences';
    protected string $primaryKey = 'conference_id';
    protected array $allowedFields = [
        'title', 'slug', 'year', 'description', 'icon',
        'is_paid', 'status', 'days', 'start_date', 'end_date'
    ];
}
