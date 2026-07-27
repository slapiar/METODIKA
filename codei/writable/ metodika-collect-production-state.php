<?php

declare(strict_types=1);

/**
 * Read-only collector for METODIKA Step 11.A.
 *
 * Placement:
 *   codei/writable/metodika-collect-production-state.php
 *
 * Run from the codei directory:
 *   php writable/metodika-collect-production-state.php
 *
 * The script performs no INSERT/UPDATE/DELETE, no migration and no deployment.
 * It bootstraps the current production application, invokes the repository's
 * DiagnosticsProductionStateInspector and prints JSON to STDOUT.
 */

use App\Services\DiagnosticsProductionStateInspector;
use Throwable;

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "ERROR: CLI_ONLY\n");
    exit(2);
}

$codeiRoot = dirname(__DIR__);
$publicPath = $codeiRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
$pathsFile = $codeiRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Paths.php';
$externalEnvironmentFile = $codeiRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'ExternalEnvironment.php';

if (! is_file($pathsFile)) {
    fwrite(STDERR, "ERROR: PATHS_FILE_NOT_FOUND\n");
    exit(3);
}

chdir($codeiRoot);

if (is_file($externalEnvironmentFile)) {
    require_once $externalEnvironmentFile;

    if (class_exists(\Config\ExternalEnvironment::class)) {
        \Config\ExternalEnvironment::load();
    }
}

$runtimeEnvironment = getenv('CI_ENVIRONMENT');
if (! is_string($runtimeEnvironment) || trim($runtimeEnvironment) === '') {
    $runtimeEnvironment = 'production';
}

$_SERVER['CI_ENVIRONMENT'] = trim($runtimeEnvironment);

require_once $codeiRoot
    . DIRECTORY_SEPARATOR . 'system'
    . DIRECTORY_SEPARATOR . 'util_bootstrap.php';

try {
    $inspector = new DiagnosticsProductionStateInspector();
    $result = $inspector->inspect();

    $envelope = [
        'collector' => [
            'name' => 'metodika-collect-production-state',
            'mode' => 'READ_ONLY',
            'collectorSha256' => hash_file('sha256', __FILE__),
            'executedAtUtc' => gmdate('c'),
        ],
        'state' => $result,
    ];

    $json = json_encode(
        $envelope,
        JSON_PRETTY_PRINT
        | JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_THROW_ON_ERROR
    );

    fwrite(STDOUT, $json . PHP_EOL);
    exit(0);
} catch (Throwable $exception) {
    $error = [
        'collector' => [
            'name' => 'metodika-collect-production-state',
            'mode' => 'READ_ONLY',
            'collectorSha256' => is_file(__FILE__) ? hash_file('sha256', __FILE__) : null,
            'executedAtUtc' => gmdate('c'),
        ],
        'error' => [
            'class' => get_class($exception),
            'code' => (string) $exception->getCode(),
            'message' => $exception->getMessage(),
        ],
    ];

    fwrite(
        STDERR,
        json_encode(
            $error,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
        ) . PHP_EOL
    );
    exit(1);
}