<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyRunState;
use App\Services\DiagnosticsConcurrencyRunStore;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DiagnosticsConcurrencyBarrierProcessTest extends CIUnitTestCase
{
    private string $baseDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('pcntl_fork')) {
            $this->markTestSkipped('Rozšírenie pcntl nie je dostupné.');
        }

        $this->baseDirectory = WRITEPATH . 'tests/concurrency-process-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        $this->deleteTree($this->baseDirectory);
        parent::tearDown();
    }

    public function testOpenedBarrierCannotBeOverwrittenByLateTimeoutAcrossTwoProcesses(): void
    {
        $runId = 'run-process-0001';
        $store = new DiagnosticsConcurrencyRunStore($this->baseDirectory);
        $store->save($runId, $this->makeDocument($runId));
        $store->mutate($runId, static function (?array $current): array {
            if (! is_array($current)) {
                throw new RuntimeException('Run nenájdený pri počiatočnom prechode.');
            }

            $current['state'] = DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER;
            $current['participants']['a']['readyAt'] = gmdate('c');

            return $current;
        });

        $pid = pcntl_fork();
        $this->assertNotSame(-1, $pid, 'Druhý proces sa nepodarilo vytvoriť.');

        if ($pid === 0) {
            try {
                $childStore = new DiagnosticsConcurrencyRunStore($this->baseDirectory);
                usleep(30_000);

                $childStore->mutate($runId, static function (?array $current): array {
                    if (! is_array($current)) {
                        throw new RuntimeException('Run nenájdený v druhom procese.');
                    }

                    $now = gmdate('c');
                    $current['participants']['b']['readyAt'] = $now;
                    $current['barrier']['openedAt'] = $now;
                    $current['state'] = DiagnosticsConcurrencyRunState::BARRIER_OPEN;

                    return $current;
                });

                $childStore->mutate($runId, static function (?array $current): array {
                    if (! is_array($current)) {
                        throw new RuntimeException('Run nenájdený pri prechode do EXECUTING.');
                    }

                    $current['participants']['b']['startedAt'] = gmdate('c');
                    $current['state'] = DiagnosticsConcurrencyRunState::EXECUTING;

                    return $current;
                });

                exit(0);
            } catch (Throwable $exception) {
                fwrite(STDERR, $exception::class . ': ' . $exception->getMessage() . PHP_EOL);
                exit(2);
            }
        }

        $projected = null;
        $deadline = microtime(true) + 2.0;
        while (microtime(true) < $deadline) {
            $candidate = $store->load($runId);
            if (is_array($candidate)
                && is_string($candidate['barrier']['openedAt'] ?? null)
                && ($candidate['state'] ?? null) === DiagnosticsConcurrencyRunState::BARRIER_OPEN
            ) {
                $projected = $candidate;
                break;
            }

            usleep(10_000);
        }

        $status = 0;
        pcntl_waitpid($pid, $status);
        $this->assertTrue(pcntl_wifexited($status));
        $this->assertSame(0, pcntl_wexitstatus($status));
        $this->assertIsArray($projected, 'Čakajúci proces nerozpoznal otvorenú bariéru.');

        $raw = $this->readRawDocument($runId);
        $this->assertSame(DiagnosticsConcurrencyRunState::EXECUTING, $raw['state']);
        $this->assertSame(DiagnosticsConcurrencyRunState::BARRIER_OPEN, $projected['state']);
        $this->assertNotNull($raw['barrier']['openedAt']);
        $this->assertNull($raw['participants']['a']['startedAt']);
        $this->assertNotNull($raw['participants']['b']['startedAt']);

        $afterTimeoutAttempt = $store->mutate($runId, static function (?array $current): array {
            if (! is_array($current)) {
                throw new RuntimeException('Run nenájdený pri timeout pokuse.');
            }

            $current['participants']['a']['errorCode'] = 'PARTNER_TIMEOUT';
            $current['participants']['a']['outcome'] = 'TIMEOUT';
            $current['participants']['a']['finishedAt'] = gmdate('c');
            $current['finalization']['claimedAt'] = gmdate('c');
            $current['finalization']['claimedBy'] = 'a';
            $current['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;

            return $current;
        });

        $this->assertSame(DiagnosticsConcurrencyRunState::EXECUTING, $afterTimeoutAttempt['state']);
        $this->assertNull($afterTimeoutAttempt['participants']['a']['errorCode']);
        $this->assertNull($afterTimeoutAttempt['participants']['a']['outcome']);
        $this->assertNull($afterTimeoutAttempt['finalization']['claimedAt']);

        $persistedAfterAttempt = $this->readRawDocument($runId);
        $this->assertSame($raw, $persistedAfterAttempt);
    }

    /** @return array<string, mixed> */
    private function readRawDocument(string $runId): array
    {
        $raw = file_get_contents($this->baseDirectory . '/' . $runId . '.json');
        $this->assertIsString($raw);

        $document = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($document);

        return $document;
    }

    /** @return array<string, mixed> */
    private function makeDocument(string $runId): array
    {
        $participant = [
            'tokenHash' => str_repeat('a', 64),
            'consumedAt' => null,
            'readyAt' => null,
            'startedAt' => null,
            'finishedAt' => null,
            'outcome' => null,
            'errorCode' => null,
        ];

        return [
            'version' => 1,
            'runId' => $runId,
            'state' => DiagnosticsConcurrencyRunState::CREATED,
            'createdAt' => gmdate('c', time() - 5),
            'expiresAt' => gmdate('c', time() + 120),
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

    private function deleteTree(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if (! is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $path . '/' . $item;
            if (is_dir($fullPath)) {
                $this->deleteTree($fullPath);
                continue;
            }

            @unlink($fullPath);
        }

        @rmdir($path);
    }
}
