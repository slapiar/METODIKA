<?php

declare(strict_types=1);

use App\Services\DatabaseCapabilityInspector;
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
        unset($_ENV['METODIKA_DIAGNOSTICS_ENABLED'], $_ENV['METODIKA_DIAGNOSTICS_TOKEN']);
        unset($_SERVER['METODIKA_DIAGNOSTICS_ENABLED'], $_SERVER['METODIKA_DIAGNOSTICS_TOKEN']);

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

        $this->withSession($session)
            ->get('/diagnostics/database')
            ->assertStatus(200)
            ->assertSee('Celkový výsledok', false)
            ->assertSee('PRIPRAVENÉ', false);
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

    private function setDiagnosticsEnv(string $enabled, string $token): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED=' . $enabled);
        putenv('METODIKA_DIAGNOSTICS_TOKEN=' . $token);
        $_ENV['METODIKA_DIAGNOSTICS_ENABLED'] = $enabled;
        $_ENV['METODIKA_DIAGNOSTICS_TOKEN'] = $token;
        $_SERVER['METODIKA_DIAGNOSTICS_ENABLED'] = $enabled;
        $_SERVER['METODIKA_DIAGNOSTICS_TOKEN'] = $token;
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
        $inspector = new class ($result) extends DatabaseCapabilityInspector {
            /** @var array<string, mixed> */
            private array $result;

            /** @param array<string, mixed> $result */
            public function __construct(array $result)
            {
                parent::__construct(static function () {
                    throw new RuntimeException('unused');
                });

                $this->result = $result;
            }

            public function inspect(): array
            {
                return $this->result;
            }
        };

        Services::injectMock('databaseCapabilityInspector', $inspector);
    }
}
