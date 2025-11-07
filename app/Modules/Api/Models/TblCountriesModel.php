<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 18:31
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblCountriesModel extends Model
{
    protected string $table          = 'tbl_countries';
    protected string $primaryKey     = 'id';
    protected string $returnType     = 'array';
    protected bool $useSoftDeletes = false;

    protected array $allowedFields  = [
        'country_name',
        'country_code',
    ];

    protected bool $useTimestamps  = false;
    protected string $dateFormat     = 'datetime';
}
