<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\DiagnosticsConcurrencyTombstoneCleaner;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use Throwable;

final class CleanupDiagnosticsRun extends BaseCommand
{
    private const SAFE_ERROR_CODES = [
        'RUN_NOT_FOUND',
        'RUN_NOT_COMPLETED',
        'RUN_NOT_REDACTED',
        'DATABASE_CLEANUP_NOT_CONFIRMED',
        'FAILED_RUN_MARKED_SUCCESS',
        'RUN_STORE_CLEANUP_POSTCHECK_FAILED',
    ];

    protected $group = 'METODIKA';
    protected $name = 'metodika:cleanup-diagnostics-run';
    protected $description = 'Mimo produkcie odstráni redigovaný dokončený diagnostický tombstone po potvrdenom DB cleanupe.';
    protected $usage = 'metodika:cleanup-diagnostics-run <runId>';
    protected $arguments = [
        'runId' => 'Identifikátor dokončeného diagnostického runu.',
    ];

    public function run(array $params)
    {
        try {
            if (ENVIRONMENT === 'production' || getenv('METODIKA_DIAGNOSTICS_CLEANUP_ENABLED') !== '1') {
                CLI::error('Interný cleanup nie je v tomto prostredí povolený.');
                return EXIT_ERROR;
            }

            $runId = $params[0] ?? null;
            if (! is_string($runId) || trim($runId) === '') {
                CLI::error('Chýba runId.');
                return EXIT_ERROR;
            }

            $result = (new DiagnosticsConcurrencyTombstoneCleaner(
                Services::diagnosticsConcurrencyRunStore(),
            ))->cleanup(trim($runId));

            CLI::write('RUN_ID=' . $result['runId'], 'green');
            CLI::write('PREVIOUS_STATE=' . $result['previousState'], 'green');
            CLI::write('DATABASE_CLEANUP_CONFIRMED=true', 'green');
            CLI::write('OVERALL_SUCCESS=' . ($result['overallSuccess'] ? 'true' : 'false'), 'green');
            CLI::write('RUN_STORE_CLEANUP_CONFIRMED=true', 'green');

            return EXIT_SUCCESS;
        } catch (Throwable $exception) {
            log_message('error', 'Internal diagnostics cleanup failed: {message}', [
                'message' => $exception->getMessage(),
            ]);
            $errorCode = in_array($exception->getMessage(), self::SAFE_ERROR_CODES, true)
                ? $exception->getMessage()
                : 'INTERNAL_CLEANUP_ERROR';
            CLI::error('INTERNAL_CLEANUP_FAILED=' . $errorCode);

            return EXIT_ERROR;
        }
    }
}
