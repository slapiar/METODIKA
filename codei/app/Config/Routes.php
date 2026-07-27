<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');

$routes->get('diagnostics/database', 'DiagnosticsController::database');
$routes->get('diagnostics/database/login', 'DiagnosticsController::loginForm');
$routes->post('diagnostics/database/login', 'DiagnosticsController::login', ['filter' => 'csrf']);
$routes->post('diagnostics/database/run', 'DiagnosticsController::run', ['filter' => 'csrf']);
$routes->post('diagnostics/database/logout', 'DiagnosticsController::logout', ['filter' => 'csrf']);
$routes->post('diagnostics/concurrency/start', 'DiagnosticsConcurrencyStartController::start', ['filter' => 'csrf']);
$routes->post('diagnostics/concurrency/hit/a', 'DiagnosticsController::hitConcurrencyA', ['filter' => 'diagnosticsSessionRelease']);
$routes->post('diagnostics/concurrency/hit/b', 'DiagnosticsController::hitConcurrencyB', ['filter' => 'diagnosticsSessionRelease']);
$routes->get('diagnostics/concurrency/result/(:segment)', 'DiagnosticsController::concurrencyResult/$1');

// Autorizovaná diagnostika GATE. Všetky cesty sú fail-closed za diagnostics session a feature flagom.
$routes->get('gate', 'GateDashboard::index', ['filter' => 'diagnosticsGate']);
$routes->get('gate/session/(:num)', 'GateDashboard::session/$1', ['filter' => 'diagnosticsGate']);
$routes->get('api/gate/sessions', 'GateSupervisor::getAllSessions', ['filter' => 'diagnosticsGate']);
$routes->post(
    'diagnostics/database/test-api',
    'DiagnosticsController::testApi',
    ['filter' => 'diagnosticsGateWrite']
);
$routes->post(
    'diagnostics/database/create-test-session',
    'DiagnosticsController::createTestSession',
    ['filter' => 'diagnosticsGateWrite']
);
$routes->post(
    'diagnostics/database/create-test-step',
    'DiagnosticsGateStepController::create',
    ['filter' => 'diagnosticsGateWrite']
);
$routes->post(
    'diagnostics/database/create-test-evidence',
    'DiagnosticsGateEvidenceController::create',
    ['filter' => 'diagnosticsGateWrite']
);

$routes->group('api/gate', static function (RouteCollection $routes): void {
    // SESSION
    $routes->post('session', 'GateSupervisor::createSession', ['filter' => 'diagnosticsGateWrite']);
    $routes->get('session/(:num)', 'GateSupervisor::getSession/$1', ['filter' => 'diagnosticsGate']);

    // STEPS
    $routes->post('session/(:num)/step', 'GateSupervisor::updateStep/$1', ['filter' => 'diagnosticsGateWrite']);
    $routes->get('session/(:num)/steps', 'GateSupervisor::getSteps/$1', ['filter' => 'diagnosticsGate']);

    // EVIDENCE
    $routes->post('step/(:num)/evidence', 'GateSupervisor::addEvidence/$1', ['filter' => 'diagnosticsGateWrite']);
    $routes->get('step/(:num)/evidence', 'GateSupervisor::getEvidence/$1', ['filter' => 'diagnosticsGate']);

    // GATE STATE — čisto čítacia cesta.
    $routes->get('session/(:num)/state', 'GateSupervisor::getGateState/$1', ['filter' => 'diagnosticsGate']);
});
