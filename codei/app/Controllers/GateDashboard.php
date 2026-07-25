<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class GateDashboard extends BaseController
{
    public function index()
    {
        return view('gate_dashboard');
    }
}
