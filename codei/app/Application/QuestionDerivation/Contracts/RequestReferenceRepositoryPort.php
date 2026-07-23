<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation\Contracts;

use App\Application\QuestionDerivation\Data\RequestReferenceReservation;
use App\Application\QuestionDerivation\Data\ReservationResult;
use DateTimeImmutable;

interface RequestReferenceRepositoryPort
{
    public function reserveFirstAcceptance(
        string $requestReference,
        string $payloadFingerprint,
        string $derivationReference,
        DateTimeImmutable $reservedAt,
    ): ReservationResult;

    public function findByRequestReference(string $requestReference): ?RequestReferenceReservation;

    public function markRunning(
        string $requestReference,
        string $derivationReference,
        DateTimeImmutable $updatedAt,
    ): void;

    public function attachCompletedResult(
        string $requestReference,
        string $derivationReference,
        string $runState,
        string $resultReference,
        DateTimeImmutable $updatedAt,
    ): void;

    /** @return array<string, mixed>|null */
    public function loadCompletedResult(string $requestReference): ?array;
}
