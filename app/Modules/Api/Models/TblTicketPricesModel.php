<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 11:02
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblTicketPricesModel extends Model
{
    protected string $table            = 'tbl_ticket_prices';
    protected string $primaryKey       = 'id';
    protected string $returnType       = 'array';
    protected array $allowedFields    = [
        'conference_slug',
        'amount_naira',
        'amount_shillings',
        'amount_rands',
        'amount_dollar',
        'status',
        'effective_from'
    ];
}
