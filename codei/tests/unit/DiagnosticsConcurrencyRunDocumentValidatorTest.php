<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyRunDocumentValidator;
use App\Services\DiagnosticsConcurrencyRunState;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DiagnosticsConcurrencyRunDocumentValidatorTest extends CIUnitTestCase
{
    private DiagnosticsConcurrencyRunDocumentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new DiagnosticsConcurrencyRunDocumentValidator();
    }

    public function testValidateAcceptsBaseCreatedDocument(): void
    {
        $document = $this->makeDocument(DiagnosticsConcurrencyRunState::CREATED);

        $this->validator->validate($document);
        $this->addToAssertionCount(1);
    }

    public function testValidateRejectsResultsReadyWithoutFinishedParticipants(): void
    {
        $document = $this->makeDocument(DiagnosticsConcurrencyRunState::RESULTS_READY);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->validator->validate($document);
    }

    public function testValidateAcceptsResultsReadyWithBothOutcomes(): void
    {
        $document = $this->makeDocument(DiagnosticsConcurrencyRunState::RESULTS_READY);
        $document['participants']['a']['finishedAt'] = '2026-07-23T12:05:00Z';
        $document['participants']['a']['outcome'] = 'CREATED';
        $document['participants']['b']['finishedAt'] = '2026-07-23T12:05:01Z';
        $document['participants']['b']['outcome'] = 'ALREADY_EXISTS';

        $this->validator->validate($document);
        $this->addToAssertionCount(1);
    }

    public function testValidateCompletedDocumentRequiresTombstoneMetadata(): void
    {
        $document = $this->makeDocument(DiagnosticsConcurrencyRunState::COMPLETED_FAILED);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->validator->validate($document);
    }

    public function testValidateCompletedDocumentAcceptsNullTokenHashesAndReadOnce(): void
    {
        $document = $this->makeDocument(DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS);
        $document['participants']['a']['tokenHash'] = null;
        $document['participants']['b']['tokenHash'] = null;
        $document['participants']['a']['finishedAt'] = '2026-07-23T12:06:00Z';
        $document['participants']['a']['outcome'] = 'CREATED';
        $document['participants']['b']['finishedAt'] = '2026-07-23T12:06:01Z';
        $document['participants']['b']['outcome'] = 'ALREADY_EXISTS';
        $document['completedAt'] = '2026-07-23T12:06:02Z';
        $document['deleteAfter'] = '2026-07-23T12:16:02Z';
        $document['readOnceConsumedAt'] = null;

        $this->validator->validate($document);
        $this->addToAssertionCount(1);
    }

    public function testValidateTransitionRejectsRunIdChange(): void
    {
        $current = $this->makeDocument(DiagnosticsConcurrencyRunState::CREATED);
        $next = $this->makeDocument(DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER);
        $next['runId'] = 'runid-other';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->validator->validateTransition($current, $next);
    }

    public function testValidateTransitionRejectsInvalidStateJump(): void
    {
        $current = $this->makeDocument(DiagnosticsConcurrencyRunState::CREATED);
        $next = $this->makeDocument(DiagnosticsConcurrencyRunState::RESULTS_READY);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->validator->validateTransition($current, $next);
    }

    public function testValidateTransitionAllowsProgressiveStateChange(): void
    {
        $current = $this->makeDocument(DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER);
        $next = $this->makeDocument(DiagnosticsConcurrencyRunState::BARRIER_OPEN);

        $this->validator->validateTransition($current, $next);
        $this->addToAssertionCount(1);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeDocument(string $state): array
    {
        $participant = [
            'tokenHash' => str_repeat('b', 64),
            'consumedAt' => null,
            'readyAt' => null,
            'startedAt' => null,
            'finishedAt' => null,
            'outcome' => null,
            'errorCode' => null,
        ];

        return [
            'version' => 1,
            'runId' => 'runid-test1',
            'state' => $state,
            'createdAt' => '2026-07-23T12:00:00Z',
            'expiresAt' => '2026-07-23T12:10:00Z',
            'participants' => [
                'a' => $participant,
                'b' => $participant,
            ],
            'barrier' => [
                'openedAt' => null,
                'waitTimeoutMs' => 2500,
            ],
            'finalization' => [
                'claimedAt' => null,
                'claimedBy' => null,
                'finishedAt' => null,
            ],
            'cleanup' => [
                'cleanupConfirmed' => false,
                'cleanupErrorCode' => null,
            ],
            'assertions' => [
                'dbUniquenessConfirmed' => null,
                'appReplayConfirmed' => null,
                'cleanupConfirmed' => null,
                'overallSuccess' => null,
            ],
        ];
    }
}
