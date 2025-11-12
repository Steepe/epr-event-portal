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
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'attendee_id', 'firstname', 'lastname', 'telephone',
        'country', 'city', 'state', 'profile_picture',
        'company', 'position', 'ipaddress', 'is_verified',
        'registration_timestamp', 'created_at', 'updated_at'
    ];

    public function getAttendees($currentUserId, $search = null)
    {
        $builder = $this->db->table($this->table)
            ->select('id, attendee_id, firstname, lastname, profile_picture, company, position, country, city, state')
            ->where('attendee_id !=', $currentUserId)
            ->orderBy('firstname', 'ASC');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('firstname', $search)
                ->orLike('lastname', $search)
                ->orLike('company', $search)
                ->orLike('position', $search)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
