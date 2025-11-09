<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:49
 */


namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class ExhibitorsModel extends Model
{
    protected string $table         = 'tbl_exhibitors';
    protected string $primaryKey    = 'id';
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
        'have_deals',
        'have_promotion',
        'views',
        'likes'
    ];
    protected bool $useTimestamps = true;
    protected string $createdField  = 'created_at';
    protected string $updatedField  = 'updated_at';
}
