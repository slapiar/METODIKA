<?php

namespace App\Models;

use CodeIgniter\Model;

class GateStateModel extends Model
{
    protected $table      = 'ini_gate_state';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'session_id',
        'state',
        'updated_at',
    ];

    protected $useTimestamps = false;
}
