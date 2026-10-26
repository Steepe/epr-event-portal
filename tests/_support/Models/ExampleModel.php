<?php

namespace Tests\Support\Models;

use CodeIgniter\Model;

class ExampleModel extends Model
{
    protected string $table          = 'factories';
    protected string $primaryKey     = 'id';
    protected string $returnType     = 'object';
    protected bool $useSoftDeletes = false;
    protected array $allowedFields  = [
        'name',
        'uid',
        'class',
        'icon',
        'summary',
    ];
    protected bool $useTimestamps      = true;
    protected array $validationRules    = [];
    protected array $validationMessages = [];
    protected $skipValidation     = false;
}
