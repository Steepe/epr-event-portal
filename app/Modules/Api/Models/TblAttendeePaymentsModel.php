<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 10:57
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblAttendeePaymentsModel extends Model
{
    protected string $table      = 'tbl_attendee_payments';
    protected string $primaryKey = 'payment_id';
    protected array $allowedFields = [
        'attendee_id',
        'conference_id',
        'amount',
        'currency',
        'transid',
        'payload',
        'paymentdate'
    ];
    protected bool $useTimestamps = false;
}
