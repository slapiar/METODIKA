<?php

namespace App\Models;

use CodeIgniter\Model;

class IniStepModel extends Model
{
    protected $table      = 'ini_steps';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'session_id',
        'step_number',
        'name',
        'status',
        'validated_at',
    ];

    protected $validationRules = [
        'session_id' => 'required|is_natural_no_zero',
        'step_number' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[15]',
        'name' => 'required|max_length[120]',
        'status' => 'required|in_list[pending,valid,invalid]',
    ];
}
