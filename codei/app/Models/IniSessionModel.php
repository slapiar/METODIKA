<?php

namespace App\Models;

use CodeIgniter\Model;

class IniSessionModel extends Model
{
    protected $table      = 'ini_sessions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'project_name',
        'agent_name',
        'gate_state',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
}
