<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @internal
 */
final class DiagnosticsAuthenticationFlowTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';

    protected function tearDown(): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED');
        putenv('METODIKA_DIAGNOSTICS_TOKEN');
        unset($_ENV['METODIKA_DIAGNOSTICS_ENABLED'], $_ENV['METODIKA_DIAGNOSTICS_TOKEN']);
        unset($_SERVER['METODIKA_DIAGNOSTICS_ENABLED'], $_SERVER['METODIKA_DIAGNOSTICS_TOKEN']);

        $_SESSION = [];
        Services::reset();

        parent::tearDown();
    }

    public function testCorrectLoginStoresAuthorizationInSession(): void
    {
        $this->setDiagnosticsEnv();

        $postData = $this->csrfPostData();
        $postData['diagnostics_token'] = 'secret-token';

        $response = $this->post('/diagnostics/database/login', $postData);

        $response->assertStatus(302);
        $response->assertRedirectTo('/diagnostics/database');

        $session = session();
        $this->assertTrue($session->get(self::AUTH_SESSION_KEY));
        $this->assertIsInt($session->get(self::AUTH_TIME_SESSION_KEY));
        $this->assertGreaterThan(0, $session->get(self::AUTH_TIME_SESSION_KEY));
    }

    public function testDatabaseRunRejectsUnauthorizedSession(): void
    {
        $this->setDiagnosticsEnv();

        $this->post('/diagnostics/database/run', $this->csrfPostData())
            ->assertStatus(404);
    }

    public function testDatabaseRunRedirectsAuthorizedSession(): void
    {
        $this->setDiagnosticsEnv();

        $response = $this->withSession($this->authorizedSession())
            ->post('/diagnostics/database/run', $this->csrfPostData());

        $response->assertStatus(302);
        $response->assertRedirectTo('/diagnostics/database');
    }

    public function testLogoutClearsAuthorizationAndRejectsFollowingAccess(): void
    {
        $this->setDiagnosticsEnv();

        $response = $this->withSession($this->authorizedSession())
            ->post('/diagnostics/database/logout', $this->csrfPostData());

        $response->assertStatus(302);
        $response->assertRedirectTo('/diagnostics/database');

        $session = session();
        $this->assertNull($session->get(self::AUTH_SESSION_KEY));
        $this->assertNull($session->get(self::AUTH_TIME_SESSION_KEY));

        $this->withSession($_SESSION)
            ->get('/diagnostics/database')
            ->assertStatus(404);
    }

    /** @return array<string, bool|int> */
    private function authorizedSession(): array
    {
        return [
            self::AUTH_SESSION_KEY => true,
            self::AUTH_TIME_SESSION_KEY => time(),
        ];
    }

    /** @return array<string, string> */
    private function csrfPostData(): array
    {
        $security = service('security');

        return [
            $security->getTokenName() => $security->getHash(),
        ];
    }

    private function setDiagnosticsEnv(): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED=1');
        putenv('METODIKA_DIAGNOSTICS_TOKEN=secret-token');
        $_ENV['METODIKA_DIAGNOSTICS_ENABLED'] = '1';
        $_ENV['METODIKA_DIAGNOSTICS_TOKEN'] = 'secret-token';
        $_SERVER['METODIKA_DIAGNOSTICS_ENABLED'] = '1';
        $_SERVER['METODIKA_DIAGNOSTICS_TOKEN'] = 'secret-token';
    }
}
