<?php

declare(strict_types=1);

namespace App\Services;

use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Application\QuestionDerivation\Data\ReservationResult;
use App\Infrastructure\Persistence\QuestionDerivation\FirstAcceptanceServiceFactory;
use Closure;
use Throwable;

final class DiagnosticsConcurrencyAcceptanceRunner
{
    /** @var Closure(string, InitialDerivationRun): string */
    private Closure $acceptor;

    /** @param Closure(string, InitialDerivationRun): string|null $acceptor */
    public function __construct(?Closure $acceptor = null)
    {
        $this->acceptor = $acceptor ?? static function (string $payloadFingerprint, InitialDerivationRun $run): string {
            $result = FirstAcceptanceServiceFactory::fromDefaultConnection()->accept($payloadFingerprint, $run);

            return match ($result->outcome) {
                ReservationResult::CREATED => 'CREATED',
                ReservationResult::ALREADY_EXISTS => 'ALREADY_EXISTS',
                default => 'FAILED_UNEXPECTED_OUTCOME',
            };
        };
    }

    public function accept(string $payloadFingerprint, InitialDerivationRun $run): string
    {
        try {
            return $this->acceptOrThrow($payloadFingerprint, $run);
        } catch (Throwable $exception) {
            $errorCode = self::classifyException($exception);

            log_message('error', 'Diagnostics acceptance failed [{code}]: {class}: {message}', [
                'code' => $errorCode,
                'class' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return 'FAILED_' . $errorCode;
        }
    }

    public function acceptOrThrow(string $payloadFingerprint, InitialDerivationRun $run): string
    {
        return ($this->acceptor)($payloadFingerprint, $run);
    }

    private static function classifyException(Throwable $exception): string
    {
        $class = $exception::class;
        $message = $exception->getMessage();

        return match (true) {
            str_contains($class, 'Database') || str_contains($message, 'database') || str_contains($message, 'SQL') => 'DATABASE_ERROR',
            $exception instanceof \TypeError => 'TYPE_ERROR',
            $exception instanceof \InvalidArgumentException => 'INPUT_ERROR',
            str_contains($message, 'JSON') => 'JSON_ERROR',
            default => 'RUNTIME_ERROR',
        };
    }
}
