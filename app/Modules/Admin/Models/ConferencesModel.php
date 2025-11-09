<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 03:03
 */

namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class ConferencesModel extends Model
{
    protected string $table = 'tbl_conferences';
    protected string $primaryKey = 'conference_id';
    protected array $allowedFields = [
        'title', 'slug', 'description', 'year',
        'is_paid', 'status', 'days',
        'start_date', 'end_date', 'icon'
    ];
    protected string $returnType = 'array';
    protected bool $useTimestamps = true;
}
