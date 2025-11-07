<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:51
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblEventsModel extends Model
{
    protected $table            = 'tbl_events';
    protected $primaryKey       = 'event_id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'event_name', 'event_slug', 'event_type', 'event_start_date',
        'event_end_date', 'event_location', 'event_banner',
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
