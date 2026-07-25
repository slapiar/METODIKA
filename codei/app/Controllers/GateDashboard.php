<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IniSessionModel;
use App\Models\IniStepModel;

class GateDashboard extends BaseController
{
    public function index()
    {
        $sessionModel = new IniSessionModel();
        $sessions = $sessionModel->orderBy('created_at', 'DESC')->findAll();

        return view('gate_dashboard', ['sessions' => $sessions]);
    }

    public function session($id)
    {
        $sessionModel = new IniSessionModel();
        $stepModel = new IniStepModel();

        $session = $sessionModel->find($id);
        $steps = $stepModel->where('session_id', $id)->orderBy('step_number')->findAll();

        return view('gate_session', [
            'session' => $session,
            'steps'   => $steps,
        ]);
    }
}
