<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class IniSessionModel extends Model
{
    protected $table = 'ini_sessions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'project_name',
        'agent_name',
        'gate_state',
    ];

    protected $useTimestamps = false;
}