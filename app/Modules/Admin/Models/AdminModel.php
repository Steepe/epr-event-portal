<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:27
 */

namespace App\Modules\Admin\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected string $table            = 'tbl_admins';
    protected string $primaryKey       = 'id';
    protected array $allowedFields    = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
        'created_at',
        'updated_at'
    ];
    protected bool $useTimestamps    = true;
    protected string $createdField     = 'created_at';
    protected string $updatedField     = 'updated_at';

    // Optionally define validation rules
    protected array $validationRules = [
        'email'    => 'required|valid_email',
        'password' => 'required|min_length[8]',
    ];

    protected array $validationMessages = [
        'email' => [
            'required'    => 'Email address is required',
            'valid_email' => 'Please enter a valid email address',
        ],
        'password' => [
            'required'   => 'Password is required',
            'min_length' => 'Password must be at least 8 characters long',
        ],
    ];

    /**
     * Find admin by email.
     */
    public function findByEmail(string $email): object|array|null
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Verify credentials for login.
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $admin = $this->findByEmail($email);

        if ($admin && password_verify($password, $admin['password']) && $admin['status'] === 'active') {
            return $admin;
        }

        return null;
    }

    /**
     * Update last login timestamp.
     * @throws \ReflectionException
     */
    public function markLogin(int $adminId): bool
    {
        return $this->update($adminId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
