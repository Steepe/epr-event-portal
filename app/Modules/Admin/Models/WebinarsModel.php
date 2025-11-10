<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 20:02
 */

namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class WebinarsModel extends Model
{
    protected string $table = 'tbl_webinars';
    protected string $primaryKey = 'event_id';

    protected array $allowedFields = [
        'event_name',
        'event_date',
        'start_time',
        'end_time',
        'is_open',
        'is_past',
        'vimeo_id',
        'zoom_link',
        'tags',
        'tags_meta'
    ];

    protected bool $useTimestamps = true;
    protected string $createdField  = 'created_at';
    protected string $updatedField  = 'updated_at';

    public function getAll(): array
    {
        return $this->orderBy('event_date', 'DESC')->findAll();
    }

    public function toggleAccess($id): bool
    {
        $webinar = $this->find($id);
        if (!$webinar) return false;

        $newStatus = $webinar['is_open'] ? 0 : 1;
        return $this->update($id, ['is_open' => $newStatus]);
    }
}
