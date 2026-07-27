<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class IniSessionModel extends Model
{
    protected $table = 'ini_sessions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'project_name',
        'agent_name',
        'gate_state',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'project_name' => 'required|max_length[120]',
        'agent_name' => 'required|max_length[120]',
        'gate_state' => 'required|in_list[locked,verifying,open]',
    ];
}
