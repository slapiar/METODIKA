<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use Throwable;
use TypeError;

final class DiagnosticsConcurrencyFailureReporter
{
    public const BUILD_INITIAL_RUN = 'BUILD_INITIAL_RUN';
    public const LOAD_PAYLOAD_FINGERPRINT = 'LOAD_PAYLOAD_FINGERPRINT';
    public const CREATE_ACCEPTANCE_RUNNER = 'CREATE_ACCEPTANCE_RUNNER';
    public const APPLICATION_ACCEPT = 'APPLICATION_ACCEPT';
    public const WRITE_PARTICIPANT_RESULT = 'WRITE_PARTICIPANT_RESULT';

    public function report(
        string $phase,
        Throwable $exception,
        string $runId,
        string $participant,
    ): string {
        $errorCode = $phase . '_' . $this->classifyException($exception);

        log_message(
            'error',
            'Diagnostics concurrency phase failed [{code}] phase={phase} runId={runId} participant={participant}: {class}: {message}',
            [
                'code' => $errorCode,
                'phase' => $phase,
                'runId' => $runId,
                'participant' => $participant,
                'class' => $exception::class,
                'message' => $exception->getMessage(),
            ],
        );

        return $errorCode;
    }

    private function classifyException(Throwable $exception): string
    {
        $class = $exception::class;
        $message = $exception->getMessage();

        return match (true) {
            str_contains($class, 'Database')
                || str_contains(strtolower($message), 'database')
                || str_contains($message, 'SQL') => 'DATABASE_ERROR',
            $exception instanceof TypeError => 'TYPE_ERROR',
            $exception instanceof InvalidArgumentException => 'INPUT_ERROR',
            str_contains(strtoupper($message), 'JSON') => 'JSON_ERROR',
            default => 'RUNTIME_ERROR',
        };
    }
}
