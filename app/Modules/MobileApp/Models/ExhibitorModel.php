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

class ExhibitorModel extends Model
{
    protected string $table = 'tbl_exhibitors';
    protected string $primaryKey = 'id';
    protected array $allowedFields = [
        'company_name',
        'contact_person',
        'email',
        'telephone',
        'website',
        'tagline',
        'vimeo_id',
        'logo',
        'profile_summary',
        'has_promotion',
        'promotion_text',
        'views',
        'likes'
    ];
}
