<?php

namespace App\Models;

use CodeIgniter\Model;

class IniEvidenceModel extends Model
{
    protected $table      = 'ini_evidence';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'step_id',
        'type',
        'content',
        'created_at',
    ];

    protected $useTimestamps = false;
}
