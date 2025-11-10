<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 03:27
 */

namespace App\Modules\Web\Models;

use CodeIgniter\Model;

class ConferenceModel extends Model
{
    protected string $table = 'tbl_conferences';
    protected string $primaryKey = 'conference_id';
    protected array $allowedFields = [
        'title', 'slug', 'year', 'description', 'icon',
        'is_paid', 'status', 'days', 'start_date', 'end_date',
        'created_at', 'updated_at'
    ];
}
