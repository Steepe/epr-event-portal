<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 06:00
 */

namespace App\Modules\MobileApp\Models;

use CodeIgniter\Model;

class SponsorModel extends Model
{
    protected string $table = 'tbl_sponsors';
    protected string $primaryKey = 'sponsor_id';
    protected array $allowedFields = [
        'conference_id', 'name', 'logo', 'link'
    ];
}
