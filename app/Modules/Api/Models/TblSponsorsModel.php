<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:52
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblSponsorsModel extends Model
{
    protected string $table = 'tbl_sponsors';
    protected string $primaryKey = 'id';
    protected string $returnType = 'array';

    protected array $allowedFields = [
        'name',
        'logo',
        'tier',       // e.g., 'Platinum', 'Gold', 'Silver'
        'website',
        'description',
        'created_at',
        'updated_at'
    ];
}
