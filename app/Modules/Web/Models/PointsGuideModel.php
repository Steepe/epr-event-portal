<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 11:53
 */


namespace App\Modules\Web\Models;

use CodeIgniter\Model;

class PointsGuideModel extends Model
{
    protected string $table = 'tbl_points_guide';
    protected string $primaryKey = 'activity_id';
    protected array $allowedFields = ['activity', 'short_name', 'image', 'points'];
}
