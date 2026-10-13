<?php

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblAttendeeConnectionsModel extends Model
{
    protected string $table = 'tbl_attendee_connections';
    protected string $primaryKey = 'id';
    protected string $returnType = 'array';

    protected array $allowedFields = [
        'requester_id',
        'receiver_id',
        'status',
        'created_at',
        'updated_at',
        'accepted_at',
    ];

    protected bool $useTimestamps = false;
}
