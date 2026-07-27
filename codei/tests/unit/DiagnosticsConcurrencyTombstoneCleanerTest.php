<?php

declare(strict_types=1);

use App\Services\DiagnosticsConcurrencyRunState;
use App\Services\DiagnosticsConcurrencyRunStore;
use App\Services\DiagnosticsConcurrencyTombstoneCleaner;
use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class DiagnosticsConcurrencyTombstoneCleanerTest extends CIUnitTestCase
{
    private string $baseDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseDirectory = WRITEPATH . 'tests/tombstone-cleaner-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        $this->deleteTree($this->baseDirectory);
        parent::tearDown();
    }

    public function testCompletedFailedTombstoneIsRemovedWithoutChangingOverallResult(): void
    {
        $runId = 'run-cleaner-failed-0001';
        $store = new DiagnosticsConcurrencyRunStore($this->baseDirectory);
        $this->seedCompletedDocument($store, $runId);

        $result = (new DiagnosticsConcurrencyTombstoneCleaner($store))->cleanup($runId);

        $this->assertSame(DiagnosticsConcurrencyRunState::COMPLETED_FAILED, $result['previousState']);
        $this->assertTrue($result['cleanupConfirmed']);
        $this->assertFalse($result['overallSuccess']);
        $this->assertFileDoesNotExist($this->baseDirectory . '/' . $runId . '.json');
        $this->assertFileDoesNotExist($this->baseDirectory . '/' . $runId . '.lock');
    }

    public function testCleanerRejectsUnfinishedRunWithoutMutation(): void
    {
        $runId = 'run-cleaner-active-0001';
        $store = new DiagnosticsConcurrencyRunStore($this->baseDirectory);
        $document = $this->activeDocument($runId);
        $store->save($runId, $document);
        $store->mutate($runId, static function (?array $current): array {
            $current['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;
            $current['finalization']['claimedAt'] = gmdate('c');
            $current['finalization']['claimedBy'] = 'a';

            return $current;
        });
        $before = $store->load($runId);

        try {
            (new DiagnosticsConcurrencyTombstoneCleaner($store))->cleanup($runId);
            $this->fail('Nedokončený run nesmie byť odstránený.');
        } catch (RuntimeException $exception) {
            $this->assertSame('RUN_NOT_COMPLETED', $exception->getMessage());
        }

        $this->assertSame($before, $store->load($runId));
    }

    /** @return array<string, mixed> */
    private function completedDocument(string $runId, string $state): array
    {
        $participant = [
            'tokenHash' => null,
            'consumedAt' => null,
            'readyAt' => null,
            'startedAt' => gmdate('c', time() - 2),
            'finishedAt' => gmdate('c', time() - 1),
            'outcome' => 'FAILED',
            'errorCode' => 'SAFE_FAILURE',
        ];

        return [
            'version' => 1,
            'runId' => $runId,
            'state' => $state,
            'createdAt' => gmdate('c', time() - 10),
            'expiresAt' => gmdate('c', time() - 5),
            'participants' => ['a' => $participant, 'b' => $participant],
            'barrier' => ['openedAt' => null, 'waitTimeoutMs' => 2500],
            'finalization' => [
                'claimedAt' => gmdate('c', time() - 3),
                'claimedBy' => 'a',
                'finishedAt' => gmdate('c', time() - 1),
            ],
            'cleanup' => ['cleanupConfirmed' => true, 'cleanupErrorCode' => null],
            'assertions' => [
                'dbUniquenessConfirmed' => true,
                'appReplayConfirmed' => false,
                'cleanupConfirmed' => true,
                'overallSuccess' => false,
            ],
            'completedAt' => gmdate('c', time() - 1),
            'deleteAfter' => gmdate('c', time() + 600),
            'readOnceConsumedAt' => null,
        ];
    }

    private function seedCompletedDocument(DiagnosticsConcurrencyRunStore $store, string $runId): void
    {
        $store->save($runId, $this->activeDocument($runId));
        $store->mutate($runId, static function (?array $current): array {
            $current['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;
            $current['finalization']['claimedAt'] = gmdate('c', time() - 2);
            $current['finalization']['claimedBy'] = 'a';

            return $current;
        });
        $store->mutate(
            $runId,
            fn (?array $current): array => $this->completedDocument(
                $runId,
                DiagnosticsConcurrencyRunState::COMPLETED_FAILED,
            ),
        );
    }

    /** @return array<string, mixed> */
    private function activeDocument(string $runId): array
    {
        $document = $this->completedDocument($runId, DiagnosticsConcurrencyRunState::CREATED);
        unset($document['completedAt'], $document['deleteAfter'], $document['readOnceConsumedAt']);

        foreach (['a', 'b'] as $participant) {
            $document['participants'][$participant] = [
                'tokenHash' => str_repeat($participant, 64),
                'consumedAt' => null,
                'readyAt' => null,
                'startedAt' => null,
                'finishedAt' => null,
                'outcome' => null,
                'errorCode' => null,
            ];
        }

        $document['finalization'] = ['claimedAt' => null, 'claimedBy' => null, 'finishedAt' => null];
        $document['cleanup'] = ['cleanupConfirmed' => false, 'cleanupErrorCode' => null];
        $document['assertions'] = [
            'dbUniquenessConfirmed' => null,
            'appReplayConfirmed' => null,
            'cleanupConfirmed' => null,
            'overallSuccess' => null,
        ];

        return $document;
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

            $child = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($child)) {
                $this->deleteTree($child);
            } else {
                @unlink($child);
            }
        }

        @rmdir($path);
    }
}
