<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:53
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblAttendeesModel extends Model
{
    protected string $table            = 'tbl_attendees';
    protected string $primaryKey       = 'id';
    protected string $returnType       = 'array';
    protected bool $useSoftDeletes   = false;
    protected array $allowedFields    = [
        'attendee_id', 'firstname', 'lastname', 'telephone', 'country',
        'city', 'state', 'useremail', 'userpassword', 'ipaddress',
        'uid', 'is_verified', 'registration_timestamp',
        'created_at', 'updated_at'
    ];
}
