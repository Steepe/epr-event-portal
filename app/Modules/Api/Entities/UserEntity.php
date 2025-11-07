<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 16:46
 */

namespace App\Modules\Api\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    // Example accessor (computed property)
    public function getFullName(): string
    {
        return trim(($this->attributes['firstname'] ?? '') . ' ' . ($this->attributes['lastname'] ?? ''));
    }

    // Example mutator
    public function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }
}
