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

$routes->group('api/gate', function($routes) {

    // EXISTUJÚCE:
    $routes->post('purge-cache/(:num)', 'GateSupervisor::purgeCache/$1');
    $routes->post('submit-step', 'GateSupervisor::submitStep');

    // SESSION
    $routes->post('session', 'GateSupervisor::createSession');
    $routes->get('session/(:num)', 'GateSupervisor::getSession/$1');

    // STEPS
    $routes->post('session/(:num)/step', 'GateSupervisor::updateStep/$1');
    $routes->get('session/(:num)/steps', 'GateSupervisor::getSteps/$1');

    // EVIDENCE
    $routes->post('step/(:num)/evidence', 'GateSupervisor::addEvidence/$1');
    $routes->get('step/(:num)/evidence', 'GateSupervisor::getEvidence/$1');

    // GATE STATE
    $routes->get('session/(:num)/state', 'GateSupervisor::getGateState/$1');
});