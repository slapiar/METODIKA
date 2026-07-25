<?php

namespace App\Models;

use CodeIgniter\Model;

class IniStepRegisterModel extends Model
{
    protected $table            = 'ini_step_register';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'session_id', 'step_number', 'step_code', 
        'executed_status', 'evidence_payload', 
        'successor_validated', 'successor_note',
        'read_flag_r', 'written_flag_w'
    ];
    protected $useTimestamps    = true;

    /**
     * Poka-Yoke kontrola: Môže sa vykonať krok N?
     * Pravidlo: Krok N sa môže vykonať IBA vtedy, ak Krok N-1 skončil True (1).
     */
    public function canExecuteStep(int $sessionId, int $stepNumber): bool
    {
        if ($stepNumber === 0) {
            return true; // Prvý krok 00 zakladá novú reláciu
        }

        $prevStep = $this->where('session_id', $sessionId)
                         ->where('step_number', $stepNumber - 1)
                         ->first();

        // Ak predošlý krok neexistuje alebo nebol vykonaný (executed_status != 1), krok N sa NESMIE spustiť
        return $prevStep && ((int)$prevStep['executed_status'] === 1);
    }
}