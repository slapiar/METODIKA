<?php

declare(strict_types=1);

use App\Services\DatabaseCapabilityInspector;
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
}
