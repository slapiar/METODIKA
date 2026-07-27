<?php

namespace App\Models;

use CodeIgniter\Model;

class IniEvidenceModel extends Model
{
    protected $table      = 'ini_evidence';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'step_id',
        'type',
        'content',
        'content_hash',
        'created_at',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'step_id' => 'required|is_natural_no_zero',
        'type' => 'required|max_length[64]|regex_match[/^[a-z0-9._-]+$/]',
        'content' => 'required|max_length[4000]',
        'content_hash' => 'required|exact_length[64]|alpha_numeric',
    ];
}
