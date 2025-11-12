<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 13:08
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class CountryModel extends Model
{
    protected string $table = 'tbl_countries';
    protected string $primaryKey = 'id';
    protected array $allowedFields = ['country_code', 'country_name'];
    public bool $timestamps = false;
}
