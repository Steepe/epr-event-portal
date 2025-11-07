<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 10:49
 */

namespace App\Modules\Auth\Models;


use CodeIgniter\Model;

class AttendeeModel extends Model
{
    protected $table = 'tbl_attendees';
    protected $primaryKey = 'attendee_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'firstname','lastname','telephone','state','zipcode','city','country','useremail','userpassword',
        'ipaddress','uid','weavy_id','weavy_token','token_expiry','profile_picture','temp_dp',
        'hear_about','is_verified','has_logged_in','plan','facebook','twitter','instagram',
        'linkedin','last_password_change','no_password_changes','registration_timestamp'
    ];
    protected $useTimestamps = false;

    public function findByEmail(string $email)
    {
        return $this->where('useremail', $email)->first();
    }

    public function findByUid($uid)
    {
        if (empty($uid)) return null;
        return $this->where('uid', $uid)->first();
    }

    public function updateWeavyToken($uid, $token, $expiry)
    {
        return $this->where('uid', $uid)->set(['weavy_token' => $token, 'token_expiry' => $expiry])->update();
    }

    public function updateWeavyId($attendee_id, $weavyId)
    {
        return $this->where('attendee_id', $attendee_id)->set(['weavy_id' => $weavyId])->update();
    }

    public function updatePasswordHash($attendee_id, $hash)
    {
        return $this->where('attendee_id', $attendee_id)->set(['userpassword' => $hash, 'last_password_change' => date('Y-m-d H:i:s')])->update();
    }

    public function markHasLoggedIn(int $attendee_id)
    {
        return $this->update($attendee_id, ['has_logged_in' => 1]);
    }}