<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IniSessionModel;
use App\Models\IniStepModel;
use App\Models\IniEvidenceModel;
use App\Models\GateStateModel;

class GateSupervisor extends BaseController
{
    public function createSession()
    {
        $data = $this->request->getJSON(true);

        $sessionModel = new IniSessionModel();
        $id = $sessionModel->insert([
            'project_name' => $data['project_name'] ?? 'UNKNOWN',
            'agent_name'   => $data['agent_name'] ?? 'AI',
            'gate_state'   => 'locked',
        ]);

        return $this->response->setJSON(['session_id' => $id]);
    }

    public function getSession($id)
    {
        $sessionModel = new IniSessionModel();
        $session = $sessionModel->find($id);

        return $this->response->setJSON($session);
    }

    public function updateStep($sessionId)
    {
        $data = $this->request->getJSON(true);

        $stepModel = new IniStepModel();
        $stepModel->save([
            'session_id'   => $sessionId,
            'step_number'  => $data['step_number'],
            'name'         => $data['name'],
            'status'       => $data['status'],
            'validated_at' => $data['status'] === 'valid' ? date('Y-m-d H:i:s') : null,
        ]);

        return $this->response->setJSON(['status' => 'step updated']);
    }

    public function getSteps($sessionId)
    {
        $stepModel = new IniStepModel();
        $steps = $stepModel->where('session_id', $sessionId)->orderBy('step_number')->findAll();

        return $this->response->setJSON($steps);
    }

    public function addEvidence($stepId)
    {
        $data = $this->request->getJSON(true);

        $evidenceModel = new IniEvidenceModel();
        $evidenceModel->insert([
            'step_id'    => $stepId,
            'type'       => $data['type'],
            'content'    => $data['content'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'evidence added']);
    }

    public function getEvidence($stepId)
    {
        $evidenceModel = new IniEvidenceModel();
        $evidence = $evidenceModel->where('step_id', $stepId)->findAll();

        return $this->response->setJSON($evidence);
    }

    public function getGateState($sessionId)
    {
        $stepModel = new IniStepModel();
        $steps = $stepModel->where('session_id', $sessionId)->findAll();

        $valid = 0;
        $invalid = false;

        foreach ($steps as $step) {
            if ($step['status'] === 'invalid') {
                $invalid = true;
                break;
            }
            if ($step['status'] === 'valid') {
                $valid++;
            }
        }

        if ($invalid) {
            $state = 'locked';
        } elseif ($valid < 15) {
            $state = 'verifying';
        } else {
            $state = 'open';
        }

        $gateStateModel = new GateStateModel();
        $gateStateModel->insert([
            'session_id' => $sessionId,
            'state'      => $state,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['state' => $state]);
    }

    public function purgeCache($sessionId)
    {
        // Tvoj projekt môže mať vlastnú logiku, zatiaľ placeholder:
        return $this->response->setJSON(['status' => 'cache purged']);
    }

    public function submitStep()
    {
        // Placeholder pre tvoju diagnostickú logiku
        return $this->response->setJSON(['status' => 'step submitted']);
    }
    public function getAllSessions()
{
    $sessionModel = new IniSessionModel();
    return $this->response->setJSON(
        $sessionModel->orderBy('created_at', 'DESC')->findAll()
    );
}
}

