<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 04:46
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected string $table = 'tbl_users';
    protected string $primaryKey = 'id';

    protected array $allowedFields = [
        'uuid',
        'email',
        'password',
        'role',
        'is_verified',
        'last_login_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected bool $useTimestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
}
