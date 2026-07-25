<?php

declare(strict_types=1);

use App\Services\DatabaseCapabilityInspector;
use App\Services\DiagnosticsConcurrencyAcceptanceRunner;
use App\Services\DiagnosticsConcurrencyRunStore;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * Nahrádza tri historické očakávania, ktoré už nezodpovedajú kontraktu
 * potvrdenému v Kroku 9: HTTP odpoveď môže byť EXECUTING, load() čakajúcemu
 * requestu projektuje BARRIER_OPEN a fyzický JSON zostáva EXECUTING.
 *
 * @internal
 */
final class DiagnosticsProjectionCompatibilityTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /** @var list<string> */
    private array $directories = [];

    protected function tearDown(): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED');
        putenv('METODIKA_DIAGNOSTICS_TOKEN');
        putenv('METODIKA_CONCURRENCY_WEB_ENABLED');
        unset($_ENV['METODIKA_DIAGNOSTICS_ENABLED'], $_ENV['METODIKA_DIAGNOSTICS_TOKEN'], $_ENV['METODIKA_CONCURRENCY_WEB_ENABLED']);
        unset($_SERVER['METODIKA_DIAGNOSTICS_ENABLED'], $_SERVER['METODIKA_DIAGNOSTICS_TOKEN'], $_SERVER['METODIKA_CONCURRENCY_WEB_ENABLED']);

        foreach ($this->directories as $directory) {
            $this->deleteTree($directory);
        }

        Services::reset();
        parent::tearDown();
    }

    public function testConcurrencyUiUsesCurrentResultRouteWithoutTrailingSlash(): void
    {
        $this->setDiagnosticsEnv();
        $this->injectReadyInspector();

        $response = $this->withSession($this->authorizedSession())
            ->get('/diagnostics/database');

        $response->assertStatus(200);
        $response->assertSee('/diagnostics/concurrency/result');
        $response->assertDontSee('/diagnostics/concurrency/result/');
    }

    public function testHitReturnsExecutingWhileLoadProjectsBarrierOpenAndRawRemainsExecuting(): void
    {
        $this->setDiagnosticsEnv();

        $storeDirectory = $this->newStoreDirectory('projection-hit');
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static fn (): string => 'CREATED'),
        );

        $runId = 'run-projection-hit-00000001';
        $tokenA = 'token-a-projection';
        $tokenB = 'token-b-projection';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB));
        $document['participants']['b']['readyAt'] = gmdate('c', time() - 1);
        $store->save($runId, $document);

        $response = $this->withSession($this->authorizedSession())
            ->post('/diagnostics/concurrency/hit/a', [
                'runId' => $runId,
                'participantToken' => $tokenA,
            ]);

        $response->assertStatus(200);
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);
        $this->assertSame('EXECUTING', $payload['state']);
        $this->assertTrue((bool) $payload['barrierOpened']);
        $this->assertFalse((bool) $payload['timeoutReached']);

        $projected = $store->load($runId);
        $this->assertIsArray($projected);
        $this->assertSame('BARRIER_OPEN', $projected['state']);
        $this->assertNotNull($projected['participants']['a']['startedAt']);
        $this->assertNotNull($projected['participants']['a']['finishedAt']);
        $this->assertSame('CREATED', $projected['participants']['a']['outcome']);

        $raw = $this->readRawDocument($storeDirectory, $runId);
        $this->assertSame('EXECUTING', $raw['state']);
        $this->assertNotNull($raw['barrier']['openedAt']);
        $this->assertNull($raw['participants']['b']['startedAt']);
    }

    public function testAcceptFailureKeepsRawExecutingAndPersistsSafePhaseCode(): void
    {
        $this->setDiagnosticsEnv();

        $storeDirectory = $this->newStoreDirectory('projection-accept-failure');
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $rawMessage = 'RAW_SECRET_APPLICATION_EXCEPTION';
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static function () use ($rawMessage): string {
                throw new RuntimeException($rawMessage);
            }),
        );

        $runId = 'run-projection-fail-0000001';
        $tokenA = 'token-a-failure';
        $tokenB = 'token-b-failure';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB));
        $document['participants']['a']['readyAt'] = gmdate('c', time() - 1);
        $document['participants']['a']['consumedAt'] = gmdate('c', time() - 1);
        $store->save($runId, $document);

        $response = $this->withSession($this->authorizedSession())
            ->post('/diagnostics/concurrency/hit/b', [
                'runId' => $runId,
                'participantToken' => $tokenB,
            ]);

        $response->assertStatus(200);
        $response->assertDontSee($rawMessage);

        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);
        $this->assertSame('EXECUTING', $payload['state']);

        $projected = $store->load($runId);
        $this->assertIsArray($projected);
        $this->assertSame('BARRIER_OPEN', $projected['state']);
        $this->assertNotNull($projected['participants']['b']['startedAt']);
        $this->assertNotNull($projected['participants']['b']['finishedAt']);
        $this->assertSame('FAILED', $projected['participants']['b']['outcome']);
        $this->assertSame('APPLICATION_ACCEPT_RUNTIME_ERROR', $projected['participants']['b']['errorCode']);
        $this->assertStringNotContainsString($rawMessage, json_encode($projected, JSON_THROW_ON_ERROR));

        $raw = $this->readRawDocument($storeDirectory, $runId);
        $this->assertSame('EXECUTING', $raw['state']);
        $this->assertSame('FAILED', $raw['participants']['b']['outcome']);
        $this->assertSame('APPLICATION_ACCEPT_RUNTIME_ERROR', $raw['participants']['b']['errorCode']);
        $this->assertStringNotContainsString($rawMessage, json_encode($raw, JSON_THROW_ON_ERROR));
    }

    /** @return array<string, bool|int> */
    private function authorizedSession(): array
    {
        return [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];
    }

    private function setDiagnosticsEnv(): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED=1');
        putenv('METODIKA_DIAGNOSTICS_TOKEN=secret-token');
        putenv('METODIKA_CONCURRENCY_WEB_ENABLED=1');
        $_ENV['METODIKA_DIAGNOSTICS_ENABLED'] = '1';
        $_ENV['METODIKA_DIAGNOSTICS_TOKEN'] = 'secret-token';
        $_ENV['METODIKA_CONCURRENCY_WEB_ENABLED'] = '1';
        $_SERVER['METODIKA_DIAGNOSTICS_ENABLED'] = '1';
        $_SERVER['METODIKA_DIAGNOSTICS_TOKEN'] = 'secret-token';
        $_SERVER['METODIKA_CONCURRENCY_WEB_ENABLED'] = '1';
    }

    private function injectReadyInspector(): void
    {
        $result = [
            'serverVersion' => '11.4.0',
            'innodb' => true,
            'utf8mb4Bin' => true,
            'datetime6' => true,
        ];

        $inspector = new DatabaseCapabilityInspector(static function () use ($result) {
            return new class($result) {
                /** @param array<string, mixed> $result */
                public function __construct(private readonly array $result)
                {
                }

                public function initialize(): void
                {
                }

                public function query(string $sql): object
                {
                    $row = [];
                    if (str_contains($sql, 'SELECT VERSION()')) {
                        $row = ['server_version' => $this->result['serverVersion']];
                    } elseif (str_contains($sql, 'INFORMATION_SCHEMA.ENGINES')) {
                        $row = ['SUPPORT' => $this->result['innodb'] ? 'YES' : 'NO'];
                    } elseif (str_contains($sql, "SHOW COLLATION LIKE 'utf8mb4_bin'")) {
                        $row = $this->result['utf8mb4Bin'] ? ['Collation' => 'utf8mb4_bin'] : [];
                    } elseif (str_contains($sql, 'CAST(')) {
                        $row = ['datetime_6' => $this->result['datetime6']
                            ? '2026-01-01 00:00:00.123456'
                            : '2026-01-01 00:00:00'];
                    }

                    return new class($row) {
                        /** @param array<string, mixed> $row */
                        public function __construct(private readonly array $row)
                        {
                        }

                        /** @return array<string, mixed> */
                        public function getRowArray(): array
                        {
                            return $this->row;
                        }
                    };
                }
            };
        });

        Services::injectMock('databaseCapabilityInspector', $inspector);
    }

    private function newStoreDirectory(string $prefix): string
    {
        $directory = WRITEPATH . 'tests/' . $prefix . '-' . bin2hex(random_bytes(4));
        $this->directories[] = $directory;

        return $directory;
    }

    /** @return array<string, mixed> */
    private function readRawDocument(string $directory, string $runId): array
    {
        $raw = file_get_contents($directory . '/' . $runId . '.json');
        $this->assertIsString($raw);

        $document = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($document);

        return $document;
    }

    /** @return array<string, mixed> */
    private function makeRunDocument(string $runId, string $tokenHashA, string $tokenHashB): array
    {
        return [
            'version' => 1,
            'runId' => $runId,
            'state' => 'CREATED',
            'createdAt' => gmdate('c', time() - 5),
            'expiresAt' => gmdate('c', time() + 120),
            'input' => [
                'requestReference' => 'req-projection-' . bin2hex(random_bytes(4)),
                'payloadFingerprint' => hash('sha256', 'payload'),
                'derivationReferenceA' => 'derivation-a-projection',
                'derivationReferenceB' => 'derivation-b-projection',
                'derivationApplicationInput' => '{"input":"projection"}',
            ],
            'participants' => [
                'a' => [
                    'tokenHash' => $tokenHashA,
                    'consumedAt' => null,
                    'readyAt' => null,
                    'startedAt' => null,
                    'finishedAt' => null,
                    'outcome' => null,
                    'errorCode' => null,
                ],
                'b' => [
                    'tokenHash' => $tokenHashB,
                    'consumedAt' => null,
                    'readyAt' => null,
                    'startedAt' => null,
                    'finishedAt' => null,
                    'outcome' => null,
                    'errorCode' => null,
                ],
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
