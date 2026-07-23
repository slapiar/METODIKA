<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation\Data;

final readonly class ReservationResult
{
    public const CREATED = 'RESERVATION_CREATED';
    public const ALREADY_EXISTS = 'ALREADY_EXISTS';

    public function __construct(
        public string $outcome,
        public RequestReferenceReservation $reservation,
    ) {
        if (! in_array($outcome, [self::CREATED, self::ALREADY_EXISTS], true)) {
            throw new \InvalidArgumentException('Neplatný výsledok rezervácie REQUEST_REFERENCE.');
        }
    }
}
