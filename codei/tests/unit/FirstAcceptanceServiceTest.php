<?php

declare(strict_types=1);

use App\Application\QuestionDerivation\Contracts\DerivationHistoryPort;
use App\Application\QuestionDerivation\Contracts\RequestReferenceRepositoryPort;
use App\Application\QuestionDerivation\Contracts\TransactionBoundaryPort;
use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Application\QuestionDerivation\Data\RequestReferenceReservation;
use App\Application\QuestionDerivation\Data\ReservationResult;
use App\Application\QuestionDerivation\FirstAcceptanceService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class FirstAcceptanceServiceTest extends CIUnitTestCase
{
    public function testCreatedReservationAlsoCreatesHistoricalRunInsideBoundary(): void
    {
        $run = $this->runData();
        $events = [];
        $reservation = $this->reservation();

        $repository = $this->repositoryReturning(
            new ReservationResult(ReservationResult::CREATED, $reservation),
            $events,
        );
        $history = new class($events) implements DerivationHistoryPort {
            public function __construct(private array &$events)
            {
            }

            public function createInitialRun(InitialDerivationRun $run): void
            {
                $this->events[] = 'history:' . $run->derivationReference;
            }
        };
        $transactions = new class($events) implements TransactionBoundaryPort {
            public function __construct(private array &$events)
            {
            }

            public function run(callable $operation): mixed
            {
                $this->events[] = 'transaction:begin';
                $result = $operation();
                $this->events[] = 'transaction:commit';
                return $result;
            }
        };

        $result = (new FirstAcceptanceService($repository, $history, $transactions))
            ->accept(str_repeat('a', 64), $run);

        $this->assertSame(ReservationResult::CREATED, $result->outcome);
        $this->assertSame([
            'transaction:begin',
            'reserve:request-1',
            'history:derivation-1',
            'transaction:commit',
        ], $events);
    }

    public function testExistingReservationDoesNotCreateAnotherHistoricalRun(): void
    {
        $run = $this->runData();
        $events = [];

        $repository = $this->repositoryReturning(
            new ReservationResult(ReservationResult::ALREADY_EXISTS, $this->reservation()),
            $events,
        );
        $history = new class($events) implements DerivationHistoryPort {
            public function __construct(private array &$events)
            {
            }

            public function createInitialRun(InitialDerivationRun $run): void
            {
                $this->events[] = 'history';
            }
        };
        $transactions = new class($events) implements TransactionBoundaryPort {
            public function __construct(private array &$events)
            {
            }

            public function run(callable $operation): mixed
            {
                $this->events[] = 'transaction:begin';
                $result = $operation();
                $this->events[] = 'transaction:commit';
                return $result;
            }
        };

        $result = (new FirstAcceptanceService($repository, $history, $transactions))
            ->accept(str_repeat('a', 64), $run);

        $this->assertSame(ReservationResult::ALREADY_EXISTS, $result->outcome);
        $this->assertSame([
            'transaction:begin',
            'reserve:request-1',
            'transaction:commit',
        ], $events);
    }

    private function repositoryReturning(ReservationResult $result, array &$events): RequestReferenceRepositoryPort
    {
        return new class($result, $events) implements RequestReferenceRepositoryPort {
            public function __construct(
                private readonly ReservationResult $result,
                private array &$events,
            ) {
            }

            public function reserveFirstAcceptance(
                string $requestReference,
                string $payloadFingerprint,
                string $derivationReference,
                DateTimeImmutable $reservedAt,
            ): ReservationResult {
                $this->events[] = 'reserve:' . $requestReference;
                return $this->result;
            }

            public function findByRequestReference(string $requestReference): ?RequestReferenceReservation
            {
                return null;
            }

            public function markRunning(
                string $requestReference,
                string $derivationReference,
                DateTimeImmutable $updatedAt,
            ): void {
            }

            public function attachCompletedResult(
                string $requestReference,
                string $derivationReference,
                string $runState,
                string $resultReference,
                DateTimeImmutable $updatedAt,
            ): void {
            }

            public function loadCompletedResult(string $requestReference): ?array
            {
                return null;
            }
        };
    }

    private function runData(): InitialDerivationRun
    {
        return new InitialDerivationRun(
            derivationReference: 'derivation-1',
            requestReference: 'request-1',
            responseTargetReference: 'response-1',
            requestSourceSnapshot: '{}',
            sourceQuestionReference: 'question-1',
            derivationSubjectReference: 'subject-1',
            purposeSnapshot: '{}',
            contextSnapshot: '{}',
            scopeSnapshot: '{}',
            domainTermReferences: ['term-1'],
            actorReference: 'actor-1',
            authorityContextSnapshot: '{}',
            runMode: 'PARTIAL_RUN_WITH_ATOMIC_GATE',
            startedAt: new DateTimeImmutable('2026-07-23 10:00:00.000001'),
        );
    }

    private function reservation(): RequestReferenceReservation
    {
        $time = new DateTimeImmutable('2026-07-23 10:00:00.000001');

        return new RequestReferenceReservation(
            requestReference: 'request-1',
            payloadFingerprint: str_repeat('a', 64),
            derivationReference: 'derivation-1',
            reservationState: 'RESERVED',
            runState: null,
            resultReference: null,
            reservedAt: $time,
            updatedAt: $time,
        );
    }
}
