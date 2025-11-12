<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 04:42
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class AttendeeModel extends Model
{
    protected string $table = 'tbl_attendees';
    protected string $primaryKey = 'attendee_id';

    protected array $allowedFields = [
        'fullname',
        'email',
        'password',
        'phone',
        'organization',
        'title',
        'country',
        'status',
        'token',
        'created_at',
        'updated_at'
    ];

    protected bool $useTimestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
}
