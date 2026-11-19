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
        'conference_id', 'name', 'logo', 'link', 'website', 'tier', 'is_featured'
    ];

    public function getAllSponsors()
    {
        $orderCase = "CASE
            WHEN tier = 'Diamond' THEN 1
            WHEN tier = 'Platinum' THEN 2
            WHEN tier = 'Gold' THEN 3
            WHEN tier = 'Silver' THEN 4
            WHEN tier = 'Bronze' THEN 5
            WHEN tier = 'Distruptor' THEN 6
            ELSE 7 END";

        return $this->orderBy('is_featured', 'DESC')
            ->orderBy($orderCase, 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
