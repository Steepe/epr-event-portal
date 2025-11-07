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

class TblPointsGuideModel extends Model
{
    protected $table            = 'tbl_points_guide';
    protected $primaryKey       = 'guide_id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'activity', 'short_name', 'image', 'points'
    ];
}
