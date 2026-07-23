<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation;

use App\Application\QuestionDerivation\Contracts\DerivationHistoryPort;
use App\Application\QuestionDerivation\Contracts\RequestReferenceRepositoryPort;
use App\Application\QuestionDerivation\Contracts\TransactionBoundaryPort;
use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Application\QuestionDerivation\Data\ReservationResult;
use RuntimeException;

final readonly class FirstAcceptanceService
{
    public function __construct(
        private RequestReferenceRepositoryPort $requestReferences,
        private DerivationHistoryPort $history,
        private TransactionBoundaryPort $transactions,
    ) {
    }

    public function accept(
        string $payloadFingerprint,
        InitialDerivationRun $run,
    ): ReservationResult {
        return $this->transactions->run(function () use ($payloadFingerprint, $run): ReservationResult {
            $reservation = $this->requestReferences->reserveFirstAcceptance(
                $run->requestReference,
                $payloadFingerprint,
                $run->derivationReference,
                $run->startedAt,
            );

            if ($reservation->outcome === ReservationResult::ALREADY_EXISTS) {
                return $reservation;
            }

            if ($reservation->outcome !== ReservationResult::CREATED) {
                throw new RuntimeException('Neznámy výsledok rezervácie prvého prijatia.');
            }

            $this->history->createInitialRun($run);

            return $reservation;
        });
    }
}
