<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation\Data;

use DateTimeImmutable;

final readonly class RequestReferenceReservation
{
    public function __construct(
        public string $requestReference,
        public string $payloadFingerprint,
        public string $derivationReference,
        public string $reservationState,
        public ?string $runState,
        public ?string $resultReference,
        public DateTimeImmutable $reservedAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
