<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:45
 */


namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class AttendeesModel extends Model
{
    protected string $table = 'tbl_attendees';
    protected string $primaryKey = 'id';

    protected array $allowedFields = [
        'attendee_id',
        'firstname',
        'lastname',
        'telephone',
        'country',
        'city',
        'state',
        'useremail',
        'userpassword',
        'ipaddress',
        'uid',
        'is_verified',
        'registration_timestamp',
        'created_at',
        'updated_at'
    ];
}
