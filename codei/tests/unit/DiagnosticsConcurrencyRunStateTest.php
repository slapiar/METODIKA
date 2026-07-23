<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyRunState;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DiagnosticsConcurrencyRunStateTest extends CIUnitTestCase
{
    public function testAllContainsKnownStatesAndIsValidChecksThem(): void
    {
        $states = DiagnosticsConcurrencyRunState::all();

        $this->assertContains(DiagnosticsConcurrencyRunState::CREATED, $states);
        $this->assertContains(DiagnosticsConcurrencyRunState::RESULTS_READY, $states);
        $this->assertContains(DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS, $states);
        $this->assertContains(DiagnosticsConcurrencyRunState::COMPLETED_FAILED, $states);
        $this->assertContains(DiagnosticsConcurrencyRunState::COMPLETED_FAILED_CLEANUP, $states);

        foreach ($states as $state) {
            $this->assertTrue(DiagnosticsConcurrencyRunState::isValid($state));
        }

        $this->assertFalse(DiagnosticsConcurrencyRunState::isValid('BROKEN'));
    }

    public function testNewDocumentCanStartOnlyInCreatedState(): void
    {
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(null, DiagnosticsConcurrencyRunState::CREATED);
        $this->addToAssertionCount(1);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        DiagnosticsConcurrencyRunState::assertTransitionAllowed(null, DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER);
    }

    public function testSameStateTransitionIsAllowed(): void
    {
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::EXECUTING,
            DiagnosticsConcurrencyRunState::EXECUTING,
        );

        $this->addToAssertionCount(1);
    }

    public function testAllowedTransitionsOnExecutionPathAreAccepted(): void
    {
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::CREATED,
            DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER,
        );
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER,
            DiagnosticsConcurrencyRunState::BARRIER_OPEN,
        );
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::BARRIER_OPEN,
            DiagnosticsConcurrencyRunState::EXECUTING,
        );
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::EXECUTING,
            DiagnosticsConcurrencyRunState::RESULTS_READY,
        );
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::RESULTS_READY,
            DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED,
        );
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED,
            DiagnosticsConcurrencyRunState::CLEANUP_PENDING,
        );
        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::CLEANUP_PENDING,
            DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS,
        );

        $this->addToAssertionCount(1);
    }

    public function testInvalidTransitionIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        DiagnosticsConcurrencyRunState::assertTransitionAllowed(
            DiagnosticsConcurrencyRunState::CREATED,
            DiagnosticsConcurrencyRunState::RESULTS_READY,
        );
    }

    public function testCompletedStatesAreTerminal(): void
    {
        foreach ([
            DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS,
            DiagnosticsConcurrencyRunState::COMPLETED_FAILED,
            DiagnosticsConcurrencyRunState::COMPLETED_FAILED_CLEANUP,
        ] as $completed) {
            try {
                DiagnosticsConcurrencyRunState::assertTransitionAllowed($completed, DiagnosticsConcurrencyRunState::CREATED);
                $this->fail('Expected invalid transition rejection for completed state: ' . $completed);
            } catch (RuntimeException $exception) {
                $this->assertSame('Run dokument nie je validny.', $exception->getMessage());
            }
        }

        $this->addToAssertionCount(1);
    }
}
