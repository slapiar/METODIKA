<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$controllerPath = $root . '/codei/app/Controllers/DiagnosticsController.php';
$testPath = $root . '/codei/tests/session/DiagnosticsControllerTest.php';

$controller = file_get_contents($controllerPath);
if (! is_string($controller)) {
    fwrite(STDERR, "Cannot read DiagnosticsController.php\n");
    exit(1);
}

$controller = str_replace(
    "use App\\Services\\DiagnosticsConcurrencyAcceptanceRunner;\n",
    "use App\\Services\\DiagnosticsConcurrencyAcceptanceRunner;\nuse App\\Services\\DiagnosticsConcurrencyFailureReporter;\n",
    $controller,
    $importCount,
);

$old = <<<'PHP'
        $outcome = 'FAILED';
        $errorCode = null;

        try {
            $run = $this->buildInitialRunFromDocument($marked, $participant);
            $payloadFingerprint = $this->payloadFingerprintFromDocument($marked);
            $outcome = $this->acceptanceRunner()->accept($payloadFingerprint, $run);
        } catch (Throwable) {
            $errorCode = 'ACCEPT_RUNTIME_ERROR';
        }

        $completedAt = gmdate('c');

        return $this->runStore()->mutate($runId, function (?array $latest) use ($participant, $outcome, $errorCode, $completedAt): array {
PHP;

$new = <<<'PHP'
        $outcome = 'FAILED';
        $errorCode = null;
        $reporter = new DiagnosticsConcurrencyFailureReporter();
        $run = null;
        $payloadFingerprint = null;
        $runner = null;

        try {
            $run = $this->buildInitialRunFromDocument($marked, $participant);
        } catch (Throwable $exception) {
            $errorCode = $reporter->report(
                DiagnosticsConcurrencyFailureReporter::BUILD_INITIAL_RUN,
                $exception,
                $runId,
                $participant,
            );
        }

        if ($errorCode === null) {
            try {
                $payloadFingerprint = $this->payloadFingerprintFromDocument($marked);
            } catch (Throwable $exception) {
                $errorCode = $reporter->report(
                    DiagnosticsConcurrencyFailureReporter::LOAD_PAYLOAD_FINGERPRINT,
                    $exception,
                    $runId,
                    $participant,
                );
            }
        }

        if ($errorCode === null) {
            try {
                $runner = $this->acceptanceRunner();
            } catch (Throwable $exception) {
                $errorCode = $reporter->report(
                    DiagnosticsConcurrencyFailureReporter::CREATE_ACCEPTANCE_RUNNER,
                    $exception,
                    $runId,
                    $participant,
                );
            }
        }

        if ($errorCode === null) {
            try {
                $outcome = $runner->acceptOrThrow($payloadFingerprint, $run);
            } catch (Throwable $exception) {
                $errorCode = $reporter->report(
                    DiagnosticsConcurrencyFailureReporter::APPLICATION_ACCEPT,
                    $exception,
                    $runId,
                    $participant,
                );
            }
        }

        $completedAt = gmdate('c');

        try {
            return $this->runStore()->mutate($runId, function (?array $latest) use ($participant, $outcome, $errorCode, $completedAt): array {
PHP;

$controller = str_replace($old, $new, $controller, $mainCount);

$oldEnd = <<<'PHP'
            return $latest;
        });
    }

    private function acceptanceRunner(): DiagnosticsConcurrencyAcceptanceRunner
PHP;

$newEnd = <<<'PHP'
                return $latest;
            });
        } catch (Throwable $exception) {
            $writeErrorCode = $reporter->report(
                DiagnosticsConcurrencyFailureReporter::WRITE_PARTICIPANT_RESULT,
                $exception,
                $runId,
                $participant,
            );

            $marked['participants'][$participant]['finishedAt'] = $completedAt;
            $marked['participants'][$participant]['outcome'] = 'FAILED';
            $marked['participants'][$participant]['errorCode'] = $writeErrorCode;

            return $marked;
        }
    }

    private function acceptanceRunner(): DiagnosticsConcurrencyAcceptanceRunner
PHP;

$controller = str_replace($oldEnd, $newEnd, $controller, $endCount);

if ($importCount !== 1 || $mainCount !== 1 || $endCount !== 1) {
    fwrite(STDERR, sprintf("Controller patch mismatch imports=%d main=%d end=%d\n", $importCount, $mainCount, $endCount));
    exit(2);
}

file_put_contents($controllerPath, $controller);

$test = file_get_contents($testPath);
if (! is_string($test)) {
    fwrite(STDERR, "Cannot read DiagnosticsControllerTest.php\n");
    exit(3);
}

$marker = "    private function setDiagnosticsEnv(string \$enabled, string \$token): void\n";
if (! str_contains($test, $marker)) {
    fwrite(STDERR, "Session test insertion marker not found\n");
    exit(4);
}

$addition = <<<'PHP'
    public function testApplicationFailurePersistsOnlySafePhaseCode(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-phase-error-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $rawMessage = 'RAW_SECRET_APPLICATION_EXCEPTION';
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static function () use ($rawMessage): string {
                throw new RuntimeException($rawMessage);
            }),
        );

        $runId = 'run-phase-error-aaaaaaaa';
        $tokenA = 'token-a-phase-error';
        $tokenB = 'token-b-phase-error';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $document['participants']['b']['readyAt'] = gmdate('c', time() - 1);
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)->post('/diagnostics/concurrency/hit/a', [
            'runId' => $runId,
            'participantToken' => $tokenA,
        ]);

        $response->assertStatus(200);
        $response->assertDontSee($rawMessage);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('FAILED', $stored['participants']['a']['outcome']);
        $this->assertSame('APPLICATION_ACCEPT_RUNTIME_ERROR', $stored['participants']['a']['errorCode']);
        $this->assertStringNotContainsString($rawMessage, json_encode($stored, JSON_THROW_ON_ERROR));

        $this->deleteTree($storeDirectory);
    }

PHP;

$test = str_replace($marker, $addition . $marker, $test, $testCount);
if ($testCount !== 1) {
    fwrite(STDERR, "Session test insertion failed\n");
    exit(5);
}

file_put_contents($testPath, $test);

echo "KROK_8_PATCH_APPLIED\n";
