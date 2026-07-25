<?php

namespace App\Models;

use CodeIgniter\Model;

class IniStepModel extends Model
{
    protected $table      = 'ini_steps';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'session_id',
        'step_number',
        'name',
        'status',
        'validated_at',
    ];
}
