<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:50
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;
use App\Modules\Api\Entities\UserEntity;

class TblUsersModel extends Model
{
    protected string $table            = 'tbl_users';
    protected string $primaryKey       = 'id';
    protected bool $useAutoIncrement = true;

    protected string $returnType       = UserEntity::class;
    protected bool $useSoftDeletes   = true;

    protected array $allowedFields    = [
        'uuid', 'email', 'password', 'role', 'is_verified',
        'last_login_at', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected bool $useTimestamps = true;
    protected string $createdField  = 'created_at';
    protected string $updatedField  = 'updated_at';
    protected string $deletedField  = 'deleted_at';
}
