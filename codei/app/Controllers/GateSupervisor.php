<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\IniSessionModel;
use App\Models\IniStepRegisterModel;

class GateSupervisor extends ResourceController
{
    protected $format = 'json';

    /**
     * 1. VYČISTENIE CACHE / RESET PAMÄTE
     * AI Agent pred novým úkonom zavolá túto metódu. 
     * Kontrolka sa prepne na ČERVENÚ (RED).
     */
    public function purgeCache($sessionId = null)
    {
        $sessionModel = new IniSessionModel();

        // Premazanie aplikačnej cache CI4
        cache()->clean();

        // Nastavenie registrácie: Cache Cleared = 1, Master Gate = RED
        $sessionModel->update($sessionId, [
            'cache_cleared'     => 1,
            'master_gate_color' => 'RED',
            'gate_status'       => 'CLOSED',
            'gate_open_token'   => null
        ]);

        return $this->respond([
            'status' => 'success',
            'master_gate_color' => 'RED',
            'message' => 'Cache purged. Memory zeroed. Master Gate LOCKED (RED).'
        ]);
    }

    /**
     * 2. EXECUTE & POKA-YOKE VALIDATION (Autorita preberajúceho kroku)
     * Krok N+1 preberá a overuje Krok N.
     */
    public function submitStep()
    {
        $json = $this->request->getJSON();
        $sessionId   = $json->session_id;
        $stepNumber  = (int)$json->step_number;
        $evidence    = $json->evidence_payload ?? [];

        $stepModel    = new IniStepRegisterModel();
        $sessionModel = new IniSessionModel();

        // 1. POKA-YOKE KONTROLA: Je predošlý krok vykonaný?
        if (!$stepModel->canExecuteStep($sessionId, $stepNumber)) {
            return $this->failForbidden("POKA-YOKE VIOLATION: Step #{$stepNumber} cannot start. Step #" . ($stepNumber - 1) . " is NOT completed (0/False).");
        }

        // 2. AUTORITA PREBERAJÚCEHO: Ak ide o Krok N+1, zhodnotí a potvrdí Krok N
        if ($stepNumber > 0) {
            $prevStepNumber = $stepNumber - 1;
            $stepModel->where('session_id', $sessionId)
                      ->where('step_number', $prevStepNumber)
                      ->set([
                          'successor_validated' => 1,
                          'successor_note'      => "Validated & Accepted by Step #{$stepNumber}"
                      ])->update();
        }

        // 3. Zápis aktuálneho kroku
        $stepModel->where('session_id', $sessionId)
                  ->where('step_number', $stepNumber)
                  ->set([
                      'executed_status'  => 1,
                      'evidence_payload' => json_encode($evidence),
                      'read_flag_r'      => 1,
                      'written_flag_w'   => 1
                  ])->update();

        // 4. VYHODNOTENIE SUPERVÍZORSKEJ KONTROLKY
        $this->evaluateMasterGate($sessionId);

        return $this->respond(['status' => 'success', 'step' => $stepNumber]);
    }

    /**
     * Súkromná metóda na prepočet svietidiel
     */
    private function evaluateMasterGate($sessionId)
    {
        $stepModel    = new IniStepRegisterModel();
        $sessionModel = new IniSessionModel();

        // Spočíta koľko úkonov je plne zelených (executed=1 AND successor_validated=1)
        $validStepsCount = $stepModel->where('session_id', $sessionId)
                                     ->where('executed_status', 1)
                                     ->where('successor_validated', 1)
                                     ->countAllResults();

        // Ak sú VŠETKY 15 úkonov (00 až 14) platné -> SUPERVÍZOR SVIETI NA ZELENO!
        if ($validStepsCount >= 15) {
            $token = hash('sha256', bin2hex(random_bytes(32)) . time());
            $sessionModel->update($sessionId, [
                'master_gate_color' => 'GREEN',
                'gate_status'       => 'OPEN',
                'gate_open_token'   => $token
            ]);
        } else {
            // Inak zostáva ŽLTÁ (rozpracované)
            $sessionModel->update($sessionId, [
                'master_gate_color' => 'YELLOW',
                'gate_status'       => 'PENDING'
            ]);
        }
    }
}