<?php

declare(strict_types=1);

use App\Services\DatabaseCapabilityInspector;
use App\Services\DiagnosticsConcurrencyAcceptanceRunner;
use App\Services\DiagnosticsConcurrencyPersistenceService;
use App\Services\DiagnosticsConcurrencyRunStore;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @internal
 */
final class DiagnosticsControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function tearDown(): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED');
        putenv('METODIKA_DIAGNOSTICS_TOKEN');
        putenv('METODIKA_CONCURRENCY_WEB_ENABLED');
        unset($_ENV['METODIKA_DIAGNOSTICS_ENABLED'], $_ENV['METODIKA_DIAGNOSTICS_TOKEN'], $_ENV['METODIKA_CONCURRENCY_WEB_ENABLED']);
        unset($_SERVER['METODIKA_DIAGNOSTICS_ENABLED'], $_SERVER['METODIKA_DIAGNOSTICS_TOKEN'], $_SERVER['METODIKA_CONCURRENCY_WEB_ENABLED']);

        Services::reset();

        parent::tearDown();
    }

    public function testDisabledReturns404(): void
    {
        $this->setDiagnosticsEnv('0', 'secret-token');

        $this->get('/diagnostics/database')->assertStatus(404);
    }

    public function testMissingTokenIsRejected(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');

        $postData = $this->csrfPostData();
        $this->post('/diagnostics/database/login', $postData)->assertStatus(404);
    }

    public function testWrongTokenIsRejected(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');

        $postData = $this->csrfPostData();
        $postData['diagnostics_token'] = 'wrong-token';

        $this->post('/diagnostics/database/login', $postData)->assertStatus(404);
    }

    public function testUnauthorizedGetReturnsFallback404Page(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');

        $response = $this->get('/diagnostics/database');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('/diagnostics/database/login');
        $response->assertDontSee('Diagnostika databazy METODIKA');
    }

    public function testLoginFormIsAvailableAtDedicatedRoute(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');

        $response = $this->get('/diagnostics/database/login');

        $response->assertStatus(200);
        $response->assertSee('Diagnostika databazy METODIKA');
        $response->assertSee('diagnostics_token');
    }

    public function testCorrectTokenAllowsDiagnosticsPage(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('0');
        $this->injectInspectorResult([
            'connection' => true,
            'serverVersion' => '8.0.39',
            'server' => true,
            'innodb' => true,
            'utf8mb4Bin' => true,
            'datetime6' => true,
            'errorCode' => null,
            'diagnosedAt' => '2026-07-22T00:00:00+00:00',
        ]);

        $postData = $this->csrfPostData();
        $postData['diagnostics_token'] = 'secret-token';

        $this->post('/diagnostics/database/login', $postData)->assertStatus(302);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)
            ->get('/diagnostics/database');

        $response->assertStatus(200);
        $response->assertSee('Celkovy vysledok');
        $response->assertSee('PRIPRAVENE');
        $response->assertDontSee('Webove subezne overenie');
    }

    public function testDiagnosticsPageShowsConcurrencyUiWhenFeatureFlagEnabled(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');
        $this->injectInspectorResult([
            'connection' => true,
            'serverVersion' => '8.0.39',
            'server' => true,
            'innodb' => true,
            'utf8mb4Bin' => true,
            'datetime6' => true,
            'errorCode' => null,
            'diagnosedAt' => '2026-07-22T00:00:00+00:00',
        ]);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)
            ->get('/diagnostics/database');

        $response->assertStatus(200);
        $response->assertSee('Webove subezne overenie');
        $response->assertSee('id="diag-concurrency-start"');
        $response->assertSee('/diagnostics/concurrency/start');
        $response->assertSee('/diagnostics/concurrency/result/');
    }

    public function testDiagnosticsOutputDoesNotContainSensitiveNamesOrDsn(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->injectInspectorResult([
            'connection' => false,
            'serverVersion' => '',
            'server' => false,
            'innodb' => false,
            'utf8mb4Bin' => false,
            'datetime6' => false,
            'errorCode' => DatabaseCapabilityInspector::ERROR_CONNECTION_FAILED,
            'diagnosedAt' => '2026-07-22T00:00:00+00:00',
        ]);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)->get('/diagnostics/database');

        $response->assertStatus(200);
        $response->assertDontSee('METODIKA_ENV_FILE');
        $response->assertDontSee('METODIKA_DIAGNOSTICS_TOKEN');
        $response->assertDontSee('database.default.password');
        $response->assertDontSee('DSN');
    }

    public function testConcurrencyStartReturns404WhenFeatureFlagIsDisabled(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('0');

        $postData = $this->csrfPostData();
        $postData['requestReference'] = 'req-disabled-1';
        $postData['derivationApplicationInput'] = '{"mode":"test"}';

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $this->withSession($session)
            ->post('/diagnostics/concurrency/start', $postData)
            ->assertStatus(404);
    }

    public function testConcurrencyStartCreatesRunAndReturnsTokens(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-start-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $postData = $this->csrfPostData();
        $postData['requestReference'] = 'req-start-1';
        $postData['derivationApplicationInput'] = '{"input":"alpha"}';

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)
            ->post('/diagnostics/concurrency/start', $postData);

        $response->assertStatus(200);

        $body = (string) $response->getBody();
        $this->assertNotSame('', trim($body));
        $this->assertStringContainsString('participantTokenA', $body);

        $normalizedBody = html_entity_decode(strip_tags($body));
        $payload = json_decode($normalizedBody, true);
        $this->assertIsArray($payload, $normalizedBody);
        $this->assertArrayHasKey('runId', $payload);
        $this->assertArrayHasKey('participantTokenA', $payload);
        $this->assertArrayHasKey('participantTokenB', $payload);

        $runId = (string) $payload['runId'];
        $this->assertSame(0, strpos($runId, 'run-'));

        $resolvedStore = Services::diagnosticsConcurrencyRunStore();
        $stored = $resolvedStore->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('CREATED', $stored['state']);
        $this->assertSame('req-start-1', $stored['input']['requestReference']);
        $this->assertSame('{"input":"alpha"}', $stored['input']['derivationApplicationInput']);
        $this->assertNotSame($payload['participantTokenA'], $stored['participants']['a']['tokenHash']);
        $this->assertNotSame($payload['participantTokenB'], $stored['participants']['b']['tokenHash']);
        $this->assertSame(hash('sha256', (string) $payload['participantTokenA']), $stored['participants']['a']['tokenHash']);
        $this->assertSame(hash('sha256', (string) $payload['participantTokenB']), $stored['participants']['b']['tokenHash']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitFlowOpensBarrierWhenPartnerIsAlreadyReady(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $runId = 'run-aaaaaaaaaaaaaaaaaaaaaaaa';
        $tokenA = 'token-a-plain';
        $tokenB = 'token-b-plain';
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static fn (): string => 'CREATED'),
        );

        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $document['participants']['b']['readyAt'] = gmdate('c', time() - 1);
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $responseA = $this->withSession($session)->post('/diagnostics/concurrency/hit/a', [
            'runId' => $runId,
            'participantToken' => $tokenA,
        ]);

        $responseA->assertStatus(200);
        $payloadA = json_decode(html_entity_decode(strip_tags((string) $responseA->getBody())), true);
        $this->assertIsArray($payloadA);
        $this->assertSame('EXECUTING', $payloadA['state']);
        $this->assertTrue((bool) $payloadA['barrierOpened']);
        $this->assertFalse((bool) $payloadA['timeoutReached']);

        $afterA = $store->load($runId);
        $this->assertIsArray($afterA);
        $this->assertSame('EXECUTING', $afterA['state']);
        $this->assertNotNull($afterA['participants']['a']['consumedAt']);
        $this->assertNotNull($afterA['participants']['a']['readyAt']);
        $this->assertNotNull($afterA['participants']['a']['startedAt']);
        $this->assertNotNull($afterA['participants']['a']['finishedAt']);
        $this->assertSame('CREATED', $afterA['participants']['a']['outcome']);
        $this->assertNull($afterA['participants']['a']['errorCode']);
        $this->assertNotNull($afterA['participants']['b']['readyAt']);
        $this->assertNotNull($afterA['barrier']['openedAt']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitTimesOutAndClaimsFinalization(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-timeout-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyPersistenceService',
            $this->makePersistenceMock([
                ['reservations' => 1, 'runs' => 1, 'domainTerms' => 2],
                ['reservations' => 0, 'runs' => 0, 'domainTerms' => 0],
            ]),
        );

        $runId = 'run-dddddddddddddddddddddddd';
        $tokenA = 'token-a-timeout';
        $tokenB = 'token-b-timeout';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $document['barrier']['waitTimeoutMs'] = 10;
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
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true);
        $this->assertIsArray($payload);
        $this->assertSame('COMPLETED_FAILED', $payload['state']);
        $this->assertFalse((bool) $payload['barrierOpened']);
        $this->assertTrue((bool) $payload['timeoutReached']);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('COMPLETED_FAILED', $stored['state']);
        $this->assertSame('a', $stored['finalization']['claimedBy']);
        $this->assertNotNull($stored['finalization']['claimedAt']);
        $this->assertNotNull($stored['finalization']['finishedAt']);
        $this->assertSame('PARTNER_TIMEOUT', $stored['participants']['a']['errorCode']);
        $this->assertSame('TIMEOUT', $stored['participants']['a']['outcome']);
        $this->assertNotNull($stored['participants']['a']['finishedAt']);
        $this->assertTrue((bool) $stored['assertions']['dbUniquenessConfirmed']);
        $this->assertFalse((bool) $stored['assertions']['appReplayConfirmed']);
        $this->assertTrue((bool) $stored['assertions']['cleanupConfirmed']);
        $this->assertFalse((bool) $stored['assertions']['overallSuccess']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitRejectsInvalidTokenWithoutRunChange(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-invalid-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $runId = 'run-bbbbbbbbbbbbbbbbbbbbbbbb';
        $tokenA = 'token-a-plain';
        $tokenB = 'token-b-plain';
        $store->save($runId, $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED'));

        $before = $store->load($runId);
        $this->assertIsArray($before);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $this->withSession($session)->post('/diagnostics/concurrency/hit/a', [
            'runId' => $runId,
            'participantToken' => 'wrong-token',
        ])->assertStatus(404);

        $after = $store->load($runId);
        $this->assertSame($before, $after);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitAcceptFailureStoresSafeErrorCode(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-accept-fail-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static function (): string {
                throw new RuntimeException('raw db exception details');
            }),
        );

        $runId = 'run-eeeeeeeeeeeeeeeeeeeeeeee';
        $tokenA = 'token-a-fail';
        $tokenB = 'token-b-fail';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $document['participants']['a']['readyAt'] = gmdate('c', time() - 1);
        $document['participants']['a']['consumedAt'] = gmdate('c', time() - 1);
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)->post('/diagnostics/concurrency/hit/b', [
            'runId' => $runId,
            'participantToken' => $tokenB,
        ]);

        $response->assertStatus(200);
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true);
        $this->assertIsArray($payload);
        $this->assertSame('EXECUTING', $payload['state']);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('EXECUTING', $stored['state']);
        $this->assertNotNull($stored['participants']['b']['startedAt']);
        $this->assertNotNull($stored['participants']['b']['finishedAt']);
        $this->assertSame('FAILED', $stored['participants']['b']['outcome']);
        $this->assertSame('ACCEPT_RUNTIME_ERROR', $stored['participants']['b']['errorCode']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitSetsResultsReadyWhenBothParticipantsFinished(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-results-ready-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static fn (): string => 'ALREADY_EXISTS'),
        );
        Services::injectMock(
            'diagnosticsConcurrencyPersistenceService',
            $this->makePersistenceMock([
                ['reservations' => 1, 'runs' => 1, 'domainTerms' => 2],
                ['reservations' => 0, 'runs' => 0, 'domainTerms' => 0],
            ]),
        );

        $runId = 'run-ffffffffffffffffffffffff';
        $tokenA = 'token-a-ready';
        $tokenB = 'token-b-ready';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $doneAt = gmdate('c', time() - 1);
        $document['participants']['a']['consumedAt'] = $doneAt;
        $document['participants']['a']['readyAt'] = $doneAt;
        $document['participants']['a']['startedAt'] = $doneAt;
        $document['participants']['a']['finishedAt'] = $doneAt;
        $document['participants']['a']['outcome'] = 'CREATED';
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)->post('/diagnostics/concurrency/hit/b', [
            'runId' => $runId,
            'participantToken' => $tokenB,
        ]);

        $response->assertStatus(200);
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true);
        $this->assertIsArray($payload);
        $this->assertSame('COMPLETED_SUCCESS', $payload['state']);
        $this->assertFalse((bool) $payload['waiterMode']);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('COMPLETED_SUCCESS', $stored['state']);
        $this->assertSame('b', $stored['finalization']['claimedBy']);
        $this->assertNotNull($stored['finalization']['claimedAt']);
        $this->assertNotNull($stored['finalization']['finishedAt']);
        $this->assertSame('ALREADY_EXISTS', $stored['participants']['b']['outcome']);
        $this->assertNotNull($stored['participants']['b']['startedAt']);
        $this->assertNotNull($stored['participants']['b']['finishedAt']);
        $this->assertTrue((bool) $stored['assertions']['dbUniquenessConfirmed']);
        $this->assertTrue((bool) $stored['assertions']['appReplayConfirmed']);
        $this->assertTrue((bool) $stored['assertions']['cleanupConfirmed']);
        $this->assertTrue((bool) $stored['assertions']['overallSuccess']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitTimeoutUsesWaiterModeWhenFinalizationAlreadyClaimed(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-waiter-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $runId = 'run-999999999999999999999999';
        $tokenA = 'token-a-waiter';
        $tokenB = 'token-b-waiter';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $document['barrier']['waitTimeoutMs'] = 10;
        $document['finalization']['claimedAt'] = gmdate('c', time() - 1);
        $document['finalization']['claimedBy'] = 'b';
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
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true);
        $this->assertIsArray($payload);
        $this->assertSame('FINALIZATION_CLAIMED', $payload['state']);
        $this->assertTrue((bool) $payload['waiterMode']);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('FINALIZATION_CLAIMED', $stored['state']);
        $this->assertSame('b', $stored['finalization']['claimedBy']);
        $this->assertArrayHasKey('waiters', $stored['finalization']);
        $this->assertIsArray($stored['finalization']['waiters']);
        $this->assertArrayHasKey('a', $stored['finalization']['waiters']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyFinalizationMarksFailedCleanupWhenCleanupThrows(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-cleanup-fail-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static fn (): string => 'ALREADY_EXISTS'),
        );
        Services::injectMock(
            'diagnosticsConcurrencyPersistenceService',
            $this->makePersistenceMock([
                ['reservations' => 1, 'runs' => 1, 'domainTerms' => 2],
                ['reservations' => 1, 'runs' => 1, 'domainTerms' => 2],
            ], true),
        );

        $runId = 'run-777777777777777777777777';
        $tokenA = 'token-a-cleanup-fail';
        $tokenB = 'token-b-cleanup-fail';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $doneAt = gmdate('c', time() - 1);
        $document['participants']['a']['consumedAt'] = $doneAt;
        $document['participants']['a']['readyAt'] = $doneAt;
        $document['participants']['a']['startedAt'] = $doneAt;
        $document['participants']['a']['finishedAt'] = $doneAt;
        $document['participants']['a']['outcome'] = 'CREATED';
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $response = $this->withSession($session)->post('/diagnostics/concurrency/hit/b', [
            'runId' => $runId,
            'participantToken' => $tokenB,
        ]);

        $response->assertStatus(200);
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true);
        $this->assertIsArray($payload);
        $this->assertSame('COMPLETED_FAILED_CLEANUP', $payload['state']);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertSame('COMPLETED_FAILED_CLEANUP', $stored['state']);
        $this->assertFalse((bool) $stored['cleanup']['cleanupConfirmed']);
        $this->assertSame('CLEANUP_FAILED', $stored['cleanup']['cleanupErrorCode']);
        $this->assertFalse((bool) $stored['assertions']['overallSuccess']);

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyResultMarksReadOnceAndReturnsRedactedTombstone(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-result-read-once-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static fn (): string => 'ALREADY_EXISTS'),
        );
        Services::injectMock(
            'diagnosticsConcurrencyPersistenceService',
            $this->makePersistenceMock([
                ['reservations' => 1, 'runs' => 1, 'domainTerms' => 2],
                ['reservations' => 0, 'runs' => 0, 'domainTerms' => 0],
            ]),
        );

        $runId = 'run-121212121212121212121212';
        $tokenA = 'token-a-result';
        $tokenB = 'token-b-result';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $doneAt = gmdate('c', time() - 1);
        $document['participants']['a']['consumedAt'] = $doneAt;
        $document['participants']['a']['readyAt'] = $doneAt;
        $document['participants']['a']['startedAt'] = $doneAt;
        $document['participants']['a']['finishedAt'] = $doneAt;
        $document['participants']['a']['outcome'] = 'CREATED';
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $this->withSession($session)->post('/diagnostics/concurrency/hit/b', [
            'runId' => $runId,
            'participantToken' => $tokenB,
        ])->assertStatus(200);

        $response = $this->withSession($session)
            ->get('/diagnostics/concurrency/result/' . $runId);

        $response->assertStatus(200);
        $payload = json_decode(html_entity_decode(strip_tags((string) $response->getBody())), true);
        $this->assertIsArray($payload);
        $this->assertSame('COMPLETED_SUCCESS', $payload['state']);
        $this->assertArrayHasKey('readOnceConsumedAt', $payload);
        $this->assertNotNull($payload['readOnceConsumedAt']);
        $this->assertArrayNotHasKey('input', $payload);
        $this->assertArrayNotHasKey('tokenHash', $payload['participants']['a']);
        $this->assertArrayNotHasKey('tokenHash', $payload['participants']['b']);

        $stored = $store->load($runId);
        $this->assertIsArray($stored);
        $this->assertNotNull($stored['readOnceConsumedAt']);
        $this->assertArrayNotHasKey('input', $stored);
        $this->assertNull($stored['participants']['a']['tokenHash']);
        $this->assertNull($stored['participants']['b']['tokenHash']);
        $this->assertFileExists($store->baseDirectory() . '/' . $runId . '.json');

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyResultSweepsExpiredTombstone(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-result-sweep-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);
        Services::injectMock(
            'diagnosticsConcurrencyAcceptanceRunner',
            new DiagnosticsConcurrencyAcceptanceRunner(static fn (): string => 'ALREADY_EXISTS'),
        );
        Services::injectMock(
            'diagnosticsConcurrencyPersistenceService',
            $this->makePersistenceMock([
                ['reservations' => 1, 'runs' => 1, 'domainTerms' => 2],
                ['reservations' => 0, 'runs' => 0, 'domainTerms' => 0],
            ]),
        );

        $runId = 'run-343434343434343434343434';
        $tokenA = 'token-a-sweep';
        $tokenB = 'token-b-sweep';
        $document = $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED');
        $doneAt = gmdate('c', time() - 1);
        $document['participants']['a']['consumedAt'] = $doneAt;
        $document['participants']['a']['readyAt'] = $doneAt;
        $document['participants']['a']['startedAt'] = $doneAt;
        $document['participants']['a']['finishedAt'] = $doneAt;
        $document['participants']['a']['outcome'] = 'CREATED';
        $store->save($runId, $document);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $this->withSession($session)->post('/diagnostics/concurrency/hit/b', [
            'runId' => $runId,
            'participantToken' => $tokenB,
        ])->assertStatus(200);

        $store->mutate($runId, static function (?array $current): array {
            $current['deleteAfter'] = gmdate('c', time() - 1);
            return $current;
        });

        $this->withSession($session)
            ->get('/diagnostics/concurrency/result/' . $runId)
            ->assertStatus(404);

        $this->assertFileDoesNotExist($store->baseDirectory() . '/' . $runId . '.json');
        $this->assertFileDoesNotExist($store->baseDirectory() . '/' . $runId . '.lock');

        $this->deleteTree($storeDirectory);
    }

    public function testConcurrencyHitRejectsDisallowedStateWithoutRunChange(): void
    {
        $this->setDiagnosticsEnv('1', 'secret-token');
        $this->setConcurrencyFlag('1');

        $storeDirectory = WRITEPATH . 'tests/concurrency-hit-state-' . bin2hex(random_bytes(4));
        $store = new DiagnosticsConcurrencyRunStore($storeDirectory);
        Services::injectMock('diagnosticsConcurrencyRunStore', $store);

        $runId = 'run-cccccccccccccccccccccccc';
        $tokenA = 'token-a-plain';
        $tokenB = 'token-b-plain';
        $store->save($runId, $this->makeRunDocument($runId, hash('sha256', $tokenA), hash('sha256', $tokenB), 'CREATED'));
        $store->mutate($runId, static function (?array $current): array {
            $current['state'] = 'BARRIER_OPEN';
            return $current;
        });

        $before = $store->load($runId);
        $this->assertIsArray($before);

        $session = [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];

        $this->withSession($session)->post('/diagnostics/concurrency/hit/a', [
            'runId' => $runId,
            'participantToken' => $tokenA,
        ])->assertStatus(404);

        $after = $store->load($runId);
        $this->assertSame($before, $after);

        $this->deleteTree($storeDirectory);
    }

    private function setDiagnosticsEnv(string $enabled, string $token): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED=' . $enabled);
        putenv('METODIKA_DIAGNOSTICS_TOKEN=' . $token);
        $_ENV['METODIKA_DIAGNOSTICS_ENABLED'] = $enabled;
        $_ENV['METODIKA_DIAGNOSTICS_TOKEN'] = $token;
        $_SERVER['METODIKA_DIAGNOSTICS_ENABLED'] = $enabled;
        $_SERVER['METODIKA_DIAGNOSTICS_TOKEN'] = $token;
    }

    private function setConcurrencyFlag(string $enabled): void
    {
        putenv('METODIKA_CONCURRENCY_WEB_ENABLED=' . $enabled);
        $_ENV['METODIKA_CONCURRENCY_WEB_ENABLED'] = $enabled;
        $_SERVER['METODIKA_CONCURRENCY_WEB_ENABLED'] = $enabled;
    }

    /**
     * @return array<string, string>
     */
    private function csrfPostData(): array
    {
        $security = service('security');

        return [
            $security->getTokenName() => $security->getHash(),
        ];
    }

    /**
     * @param array<string, mixed> $result
     */
    private function injectInspectorResult(array $result): void
    {
        $inspector = new DatabaseCapabilityInspector(static function () use ($result) {
            if (($result['connection'] ?? false) !== true) {
                throw new RuntimeException('connection failed');
            }

            return new class ($result) {
                /** @param array<string, mixed> $result */
                public function __construct(private readonly array $result)
                {
                }

                public function initialize(): void
                {
                }

                public function query(string $sql): object
                {
                    if (($this->result['errorCode'] ?? null) === DatabaseCapabilityInspector::ERROR_QUERY_FAILED) {
                        throw new RuntimeException('query failed');
                    }

                    $row = [];
                    if (str_contains($sql, 'SELECT VERSION()')) {
                        $row = ['server_version' => (string) ($this->result['serverVersion'] ?? '')];
                    } elseif (str_contains($sql, 'INFORMATION_SCHEMA.ENGINES')) {
                        $row = ['SUPPORT' => ($this->result['innodb'] ?? false) ? 'YES' : 'NO'];
                    } elseif (str_contains($sql, "SHOW COLLATION LIKE 'utf8mb4_bin'")) {
                        $row = ($this->result['utf8mb4Bin'] ?? false) ? ['Collation' => 'utf8mb4_bin'] : [];
                    } elseif (str_contains($sql, 'CAST(')) {
                        $row = ['datetime_6' => ($this->result['datetime6'] ?? false)
                            ? '2026-01-01 00:00:00.123456'
                            : '2026-01-01 00:00:00'];
                    }

                    return new class ($row) {
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

    /**
     * @param list<array{reservations:int, runs:int, domainTerms:int}> $counts
     */
    private function makePersistenceMock(array $counts, bool $throwOnCleanup = false): DiagnosticsConcurrencyPersistenceService
    {
        $index = 0;

        return new DiagnosticsConcurrencyPersistenceService(
            static function (string $requestReference) use (&$index, $counts): array {
                if ($requestReference === '') {
                    throw new RuntimeException('missing request reference');
                }

                $selected = $counts[$index] ?? end($counts);
                if ($index < (count($counts) - 1)) {
                    $index++;
                }

                return [
                    'reservations' => (int) $selected['reservations'],
                    'runs' => (int) $selected['runs'],
                    'domainTerms' => (int) $selected['domainTerms'],
                ];
            },
            static function (string $requestReference) use ($throwOnCleanup): void {
                if ($requestReference === '') {
                    throw new RuntimeException('missing request reference');
                }

                if ($throwOnCleanup) {
                    throw new RuntimeException('cleanup failed');
                }
            },
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function makeRunDocument(string $runId, string $tokenHashA, string $tokenHashB, string $state): array
    {
        return [
            'version' => 1,
            'runId' => $runId,
            'state' => $state,
            'createdAt' => gmdate('c', time() - 5),
            'expiresAt' => gmdate('c', time() + 120),
            'input' => [
                'requestReference' => 'req-test-' . bin2hex(random_bytes(4)),
                'payloadFingerprint' => hash('sha256', 'payload'),
                'derivationReferenceA' => 'derivation-a-test',
                'derivationReferenceB' => 'derivation-b-test',
                'derivationApplicationInput' => '{"input":"alpha"}',
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
}
