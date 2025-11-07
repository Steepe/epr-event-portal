<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 11:55
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblConferencesModel extends Model
{
    protected string $table = 'tbl_conferences';
    protected string $primaryKey = 'conference_id';
    protected array $allowedFields = ['name', 'slug', 'is_paid', 'start_date', 'end_date'];
    protected string $returnType = 'array';
}
