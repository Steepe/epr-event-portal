<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 01/11/2025
 * Time: 19:05
 */


namespace App\Modules\Api\Models;

use CodeIgniter\Model;

class TblExhibitorsModel extends Model
{
    protected string $table = 'tbl_exhibitors';
    protected string $primaryKey = 'id';
    protected string $returnType = 'array';
    protected array $allowedFields = [
        'company_name', 'contact_person', 'email', 'telephone', 'website',
        'tagline', 'video_url', 'logo', 'profile_summary',
        'have_deals', 'have_promotion', 'views', 'likes',
        'created_at', 'updated_at'
    ];
}
