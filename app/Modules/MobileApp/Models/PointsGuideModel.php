<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 11:50
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class PointsGuideModel extends Model
{
    protected string $table = 'tbl_points_guide';
    protected string $primaryKey = 'guide_id';
    protected array $allowedFields = ['activity', 'short_name', 'image', 'points'];
}
