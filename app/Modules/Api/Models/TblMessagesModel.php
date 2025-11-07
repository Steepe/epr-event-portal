<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 22:48
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblMessagesModel extends Model
{
    protected string $table = 'tbl_messages';
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'sender_id', 'receiver_id', 'message', 'is_read', 'created_at'
    ];
    protected bool $useTimestamps = false;
}
