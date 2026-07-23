<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyRunStore;
use App\Services\DiagnosticsConcurrencyRunState;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DiagnosticsConcurrencyRunStoreTest extends CIUnitTestCase
{
    private string $baseDirectory;

    private DiagnosticsConcurrencyRunStore $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseDirectory = WRITEPATH . 'tests/concurrency-store-' . bin2hex(random_bytes(4));
        $this->store = new DiagnosticsConcurrencyRunStore($this->baseDirectory);
    }

    protected function tearDown(): void
    {
        $this->deleteTree($this->baseDirectory);
        parent::tearDown();
    }

    public function testSaveAndLoadRoundTripCreatesExpectedFiles(): void
    {
        $runId = 'runid-0001';

        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));
        $loaded = $this->store->load($runId);

        $this->assertIsArray($loaded);
        $this->assertSame(DiagnosticsConcurrencyRunState::CREATED, $loaded['state']);
        $this->assertSame($runId, $loaded['runId']);
        $this->assertFileExists($this->baseDirectory . '/' . $runId . '.lock');
        $this->assertFileExists($this->baseDirectory . '/' . $runId . '.json');
    }

    public function testMutatePerformsAtomicReadModifyWrite(): void
    {
        $runId = 'runid-0002';
        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));

        $result = $this->store->mutate($runId, static function (?array $current): array {
            $current['state'] = DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER;
            $current['participants']['a']['readyAt'] = '2026-07-23T12:01:00Z';
            return $current;
        });

        $this->assertSame(DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER, $result['state']);
        $this->assertSame('2026-07-23T12:01:00Z', $result['participants']['a']['readyAt']);
        $this->assertSame($result, $this->store->load($runId));

        $tempFiles = glob($this->baseDirectory . '/' . $runId . '.json.tmp.*');
        $this->assertIsArray($tempFiles);
        $this->assertCount(0, $tempFiles);
    }

    public function testStableLockFilePersistsAcrossAtomicJsonReplace(): void
    {
        $runId = 'runid-0003';
        $jsonPath = $this->baseDirectory . '/' . $runId . '.json';
        $lockPath = $this->baseDirectory . '/' . $runId . '.lock';

        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));

        $lockStatBefore = @stat($lockPath);
        $jsonStatBefore = @stat($jsonPath);

        $this->assertIsArray($lockStatBefore);
        $this->assertIsArray($jsonStatBefore);

        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));

        $lockStatAfter = @stat($lockPath);
        $jsonStatAfter = @stat($jsonPath);

        $this->assertIsArray($lockStatAfter);
        $this->assertIsArray($jsonStatAfter);
        $this->assertSame($lockStatBefore['ino'], $lockStatAfter['ino']);
        $this->assertNotSame($jsonStatBefore['ino'], $jsonStatAfter['ino']);
    }

    public function testInvalidRunIdIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Neplatny runId format.');

        $this->store->save('../bad', $this->makeDocument('runid-aaaa', DiagnosticsConcurrencyRunState::CREATED));
    }

    public function testCleanupIsIdempotent(): void
    {
        $runId = 'runid-0004';
        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));

        $this->store->cleanup($runId);
        $this->store->cleanup($runId);

        $this->assertFileDoesNotExist($this->baseDirectory . '/' . $runId . '.json');
        $this->assertFileDoesNotExist($this->baseDirectory . '/' . $runId . '.lock');
    }

    public function testSaveRejectsInvalidDocumentState(): void
    {
        $runId = 'runid-0005';
        $document = $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED);
        $document['state'] = 'BROKEN_STATE';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->store->save($runId, $document);
    }

    public function testMutateRejectsInvalidTransition(): void
    {
        $runId = 'runid-0006';
        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->store->mutate($runId, static function (?array $current): array {
            $current['state'] = DiagnosticsConcurrencyRunState::RESULTS_READY;
            return $current;
        });
    }

    public function testLoadRejectsUnknownStoredState(): void
    {
        $runId = 'runid-0007';
        $this->store->save($runId, $this->makeDocument($runId, DiagnosticsConcurrencyRunState::CREATED));

        $jsonPath = $this->baseDirectory . '/' . $runId . '.json';
        $raw = file_get_contents($jsonPath);
        $this->assertIsString($raw);

        $doc = json_decode($raw, true);
        $this->assertIsArray($doc);
        $doc['state'] = 'NOT_ALLOWED';
        $written = file_put_contents($jsonPath, json_encode($doc, JSON_THROW_ON_ERROR));
        $this->assertNotFalse($written);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Run dokument nie je validny.');

        $this->store->load($runId);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeDocument(string $runId, string $state): array
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
