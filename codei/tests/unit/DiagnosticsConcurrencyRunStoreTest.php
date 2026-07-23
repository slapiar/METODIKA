<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyRunStore;
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

        $this->store->save($runId, ['state' => 'CREATED', 'value' => 1]);
        $loaded = $this->store->load($runId);

        $this->assertIsArray($loaded);
        $this->assertSame('CREATED', $loaded['state']);
        $this->assertSame(1, $loaded['value']);
        $this->assertFileExists($this->baseDirectory . '/' . $runId . '.lock');
        $this->assertFileExists($this->baseDirectory . '/' . $runId . '.json');
    }

    public function testMutatePerformsAtomicReadModifyWrite(): void
    {
        $runId = 'runid-0002';
        $this->store->save($runId, ['count' => 1]);

        $result = $this->store->mutate($runId, static function (?array $current): array {
            $count = (int) ($current['count'] ?? 0);
            $current['count'] = $count + 1;
            $current['state'] = 'WAITING_FOR_PARTNER';
            return $current;
        });

        $this->assertSame(2, $result['count']);
        $this->assertSame('WAITING_FOR_PARTNER', $result['state']);
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

        $this->store->save($runId, ['version' => 1]);

        $lockStatBefore = @stat($lockPath);
        $jsonStatBefore = @stat($jsonPath);

        $this->assertIsArray($lockStatBefore);
        $this->assertIsArray($jsonStatBefore);

        $this->store->save($runId, ['version' => 2]);

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

        $this->store->save('../bad', ['state' => 'CREATED']);
    }

    public function testCleanupIsIdempotent(): void
    {
        $runId = 'runid-0004';
        $this->store->save($runId, ['state' => 'COMPLETED_FAILED']);

        $this->store->cleanup($runId);
        $this->store->cleanup($runId);

        $this->assertFileDoesNotExist($this->baseDirectory . '/' . $runId . '.json');
        $this->assertFileDoesNotExist($this->baseDirectory . '/' . $runId . '.lock');
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
