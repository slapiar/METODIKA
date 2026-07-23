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
$routes->post('diagnostics/concurrency/start', 'DiagnosticsController::startConcurrencyRun', ['filter' => 'csrf']);
$routes->post('diagnostics/concurrency/hit/a', 'DiagnosticsController::hitConcurrencyA');
$routes->post('diagnostics/concurrency/hit/b', 'DiagnosticsController::hitConcurrencyB');
$routes->get('diagnostics/concurrency/result/(:segment)', 'DiagnosticsController::concurrencyResult/$1');
