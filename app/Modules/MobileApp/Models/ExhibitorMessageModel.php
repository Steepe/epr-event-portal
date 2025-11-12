<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 10:36
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class ExhibitorMessageModel extends Model
{
    protected string $table = 'tbl_exhibitor_messages';
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'attendee_id',
        'exhibitor_id',
        'message',
        'is_from_exhibitor',
        'is_read',
        'sent_at'
    ];
}
