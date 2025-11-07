<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:52
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblPointsEarnedModel extends Model
{
    protected $table            = 'tbl_points_earned';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id', 'short_name', 'activity_id', 'points', 'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}
