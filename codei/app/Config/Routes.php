<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');

$routes->get('diagnostics/database', 'DiagnosticsController::database');
$routes->post('diagnostics/database/login', 'DiagnosticsController::login', ['filter' => 'csrf']);
$routes->post('diagnostics/database/run', 'DiagnosticsController::run', ['filter' => 'csrf']);
$routes->post('diagnostics/database/logout', 'DiagnosticsController::logout', ['filter' => 'csrf']);
