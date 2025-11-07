<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 01/11/2025
 * Time: 20:38
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblExhibitorMessagesModel extends Model
{
    protected string $table = 'tbl_exhibitor_messages';
    protected string $primaryKey = 'id';
    protected string $returnType = 'array';
    protected array $allowedFields = [
        'attendee_id', 'exhibitor_id', 'message', 'is_from_exhibitor', 'is_read', 'sent_at'
    ];
}
