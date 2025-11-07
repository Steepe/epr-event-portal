<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 22:49
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblUserStatusModel extends Model
{
    protected string $table = 'tbl_user_status';
    protected string $primaryKey = 'user_id';
    protected array $allowedFields = [
        'user_id',
        'is_online',
        'last_seen',
        'socket_id'
    ];
    protected bool $useTimestamps = false;
}
