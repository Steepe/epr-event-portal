<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 22/11/2025
 * Time: 13:12
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblPremiumPricesModel extends Model
{
    protected string $table = 'tbl_premium_prices';
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'currency', 'amount', 'country', 'active', 'updated_at'
    ];
    protected string $returnType = 'array';
}
