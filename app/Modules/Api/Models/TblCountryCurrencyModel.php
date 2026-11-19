<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 22/11/2025
 * Time: 13:16
 */

namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblCountryCurrencyModel extends Model
{
    protected string $table = 'tbl_country_currency';
    protected string $primaryKey = 'country';
    protected array $allowedFields = ['country','currency'];
    protected string $returnType = 'array';
}
