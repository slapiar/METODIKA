<?php

declare(strict_types=1);

namespace App\Services;

use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Application\QuestionDerivation\Data\ReservationResult;
use App\Infrastructure\Persistence\QuestionDerivation\FirstAcceptanceServiceFactory;
use Closure;

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
                default => 'FAILED',
            };
        };
    }

    public function accept(string $payloadFingerprint, InitialDerivationRun $run): string
    {
        return ($this->acceptor)($payloadFingerprint, $run);
    }
}
