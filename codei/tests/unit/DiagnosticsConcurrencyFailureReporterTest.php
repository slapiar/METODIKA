<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyFailureReporter;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DiagnosticsConcurrencyFailureReporterTest extends CIUnitTestCase
{
    /** @dataProvider phaseProvider */
    public function testEachPhaseProducesDistinctSafeCode(string $phase): void
    {
        $reporter = new DiagnosticsConcurrencyFailureReporter();
        $rawMessage = 'RAW_SECRET_EXCEPTION_' . $phase;

        $code = $reporter->report(
            $phase,
            new RuntimeException($rawMessage),
            'run-test-phase',
            'a',
        );

        $this->assertSame($phase . '_RUNTIME_ERROR', $code);
        $this->assertStringNotContainsString($rawMessage, $code);
    }

    public static function phaseProvider(): array
    {
        return [
            'build initial run' => [DiagnosticsConcurrencyFailureReporter::BUILD_INITIAL_RUN],
            'load fingerprint' => [DiagnosticsConcurrencyFailureReporter::LOAD_PAYLOAD_FINGERPRINT],
            'create runner' => [DiagnosticsConcurrencyFailureReporter::CREATE_ACCEPTANCE_RUNNER],
            'application accept' => [DiagnosticsConcurrencyFailureReporter::APPLICATION_ACCEPT],
            'write participant result' => [DiagnosticsConcurrencyFailureReporter::WRITE_PARTICIPANT_RESULT],
        ];
    }

    public function testExceptionClassIsReducedToSafeClassification(): void
    {
        $reporter = new DiagnosticsConcurrencyFailureReporter();

        $this->assertSame(
            'APPLICATION_ACCEPT_INPUT_ERROR',
            $reporter->report(
                DiagnosticsConcurrencyFailureReporter::APPLICATION_ACCEPT,
                new InvalidArgumentException('raw invalid input'),
                'run-input',
                'b',
            ),
        );

        $this->assertSame(
            'LOAD_PAYLOAD_FINGERPRINT_JSON_ERROR',
            $reporter->report(
                DiagnosticsConcurrencyFailureReporter::LOAD_PAYLOAD_FINGERPRINT,
                new RuntimeException('RAW JSON parse failure'),
                'run-json',
                'a',
            ),
        );
    }
}
