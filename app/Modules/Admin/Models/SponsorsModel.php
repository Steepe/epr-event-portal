<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 15:32
 */

namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class SponsorsModel extends Model
{
    protected string $table = 'tbl_sponsors';
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'name',
        'logo',
        'tier',
        'is_featured',
        'website',
        'description',
    ];
    protected bool $useTimestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';

}