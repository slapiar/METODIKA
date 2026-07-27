<?php

declare(strict_types=1);

use App\Models\GateStateModel;
use App\Models\IniEvidenceModel;
use App\Models\IniSessionModel;
use App\Models\IniStepModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use CodeIgniter\Security\Exceptions\SecurityException;
use Config\Services;

/** @internal */
final class GateDiagnosticsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = 'App';
    protected $refresh = true;

    protected function tearDown(): void
    {
        if ($this->db !== null) {
            foreach (['ini_evidence', 'ini_gate_state', 'ini_steps', 'ini_sessions'] as $table) {
                if ($this->db->tableExists($table)) {
                    $this->db->table($table)->emptyTable();
                }
            }
        }

        foreach (['METODIKA_DIAGNOSTICS_ENABLED', 'METODIKA_DIAGNOSTICS_TOKEN', 'METODIKA_GATE_ENABLED'] as $name) {
            putenv($name);
            unset($_ENV[$name], $_SERVER[$name]);
        }

        Services::reset();

        parent::tearDown();
    }

    public function testGateRoutesAreFailClosedWithoutFlagAndAuthorization(): void
    {
        $this->setGateEnvironment('1', '0');
        $this->withSession($this->authorizedSession())->get('/gate')->assertStatus(404);

        $this->setGateEnvironment('1', '1');
        $this->withSession([])->get('/gate')->assertStatus(404);
        $this->withSession([])->get('/api/gate/sessions')->assertStatus(404);
    }

    public function testDashboardAndPureGateStateReadRequireAuthorizedDiagnostics(): void
    {
        $this->setGateEnvironment('1', '1');
        $sessionId = (int) (new IniSessionModel())->insert([
            'project_name' => 'METODIKA',
            'agent_name' => 'Joyee',
            'gate_state' => 'locked',
        ]);

        $dashboard = $this->withSession($this->authorizedSession())->get('/gate');
        $dashboard->assertStatus(200);
        $dashboard->assertSee('INI Gatekeeper');
        $dashboard->assertSee('data-sessions-url=');

        $detail = $this->withSession($this->authorizedSession())->get('/gate/session/' . $sessionId);
        $detail->assertStatus(200);
        $detail->assertSee('Session #' . $sessionId);

        $before = (new GateStateModel())->countAllResults();
        $first = $this->withSession($this->authorizedSession())
            ->get('/api/gate/session/' . $sessionId . '/state');
        $second = $this->withSession($this->authorizedSession())
            ->get('/api/gate/session/' . $sessionId . '/state');

        $first->assertStatus(200);
        $second->assertStatus(200);
        $this->assertSame($before, (new GateStateModel())->countAllResults());
        $this->assertSame('verifying', $this->json($first)['state']);
        $this->assertSame('verifying', $this->json($second)['state']);
    }

    public function testGateWritesRejectMissingCsrf(): void
    {
        $this->setGateEnvironment('1', '1');
        $this->expectException(SecurityException::class);

        $this->withBodyFormat('json')
            ->withSession($this->authorizedSession())
            ->post('/api/gate/session', [
                'project_name' => 'METODIKA',
                'agent_name' => 'Joyee',
            ]);
    }

    public function testGateWritesValidateInputsAndAreIdempotent(): void
    {
        $this->setGateEnvironment('1', '1');
        $auth = $this->authorizedSession();

        $csrfHash = service('security')->getHash();
        $createdSession = $this->jsonPost('/api/gate/session', [
            'project_name' => 'METODIKA',
            'agent_name' => 'Joyee',
        ], $csrfHash, $auth);
        $createdSession->assertStatus(201);
        $sessionPayload = $this->json($createdSession);
        $sessionId = (int) $sessionPayload['session_id'];
        $csrfHash = (string) $sessionPayload['csrfHash'];

        $invalid = $this->jsonPost('/api/gate/session/' . $sessionId . '/step', [
            'step_number' => 99,
            'name' => '<script>alert(1)</script>',
            'status' => 'unknown',
        ], $csrfHash, $auth);
        $invalid->assertStatus(422);
        $this->assertSame(0, (new IniStepModel())->where('session_id', $sessionId)->countAllResults());
        $csrfHash = (string) $this->json($invalid)['csrfHash'];

        $firstStep = $this->jsonPost('/api/gate/session/' . $sessionId . '/step', [
            'step_number' => 1,
            'name' => 'Inicializácia',
            'status' => 'valid',
        ], $csrfHash, $auth);
        $firstStep->assertStatus(201);
        $stepPayload = $this->json($firstStep);
        $stepId = (int) $stepPayload['step_id'];
        $csrfHash = (string) $stepPayload['csrfHash'];

        $sameStep = $this->jsonPost('/api/gate/session/' . $sessionId . '/step', [
            'step_number' => 1,
            'name' => 'Inicializácia',
            'status' => 'valid',
        ], $csrfHash, $auth);
        $sameStep->assertStatus(200);
        $sameStepPayload = $this->json($sameStep);
        $this->assertFalse((bool) $sameStepPayload['created']);
        $this->assertSame(1, (new IniStepModel())->where('session_id', $sessionId)->countAllResults());
        $csrfHash = (string) $sameStepPayload['csrfHash'];

        $evidence = [
            'type' => 'diagnostic',
            'content' => 'Krok 11 integračný dôkaz',
        ];
        $firstEvidence = $this->jsonPost(
            '/api/gate/step/' . $stepId . '/evidence',
            $evidence,
            $csrfHash,
            $auth,
        );
        $firstEvidence->assertStatus(201);
        $evidencePayload = $this->json($firstEvidence);
        $csrfHash = (string) $evidencePayload['csrfHash'];

        $sameEvidence = $this->jsonPost(
            '/api/gate/step/' . $stepId . '/evidence',
            $evidence,
            $csrfHash,
            $auth,
        );
        $sameEvidence->assertStatus(200);
        $this->assertFalse((bool) $this->json($sameEvidence)['created']);
        $this->assertSame(1, (new IniEvidenceModel())->where('step_id', $stepId)->countAllResults());
    }

    public function testDiagnosticButtonsUseSessionOwnedIdsAndNeverRawExceptions(): void
    {
        $this->setGateEnvironment('1', '1');
        $auth = $this->authorizedSession();
        $csrfHash = service('security')->getHash();

        $missingStep = $this->formPost(
            '/diagnostics/database/create-test-step',
            $csrfHash,
            $auth,
        );
        $missingStep->assertStatus(409);
        $missingPayload = $this->json($missingStep);
        $this->assertSame('GATE_TEST_SESSION_REQUIRED', $missingPayload['errorCode']);
        $this->assertArrayNotHasKey('exception', $missingPayload);
        $this->assertArrayNotHasKey('message', $missingPayload);
        $csrfHash = (string) $missingPayload['csrfHash'];

        $created = $this->formPost(
            '/diagnostics/database/create-test-session',
            $csrfHash,
            $auth,
        );
        $created->assertStatus(201);
        $payload = $this->json($created);
        $sessionId = (int) $payload['session_id'];

        $ownedAuth = $auth + ['metodika_gate_test_session_id' => $sessionId];
        $step = $this->formPost(
            '/diagnostics/database/create-test-step',
            (string) $payload['csrfHash'],
            $ownedAuth,
        );
        $step->assertStatus(201);
        $stepPayload = $this->json($step);
        $this->assertSame($sessionId, (int) $stepPayload['session_id']);

        $ownedAuth['metodika_gate_test_step_id'] = (int) $stepPayload['step_id'];
        $evidence = $this->formPost(
            '/diagnostics/database/create-test-evidence',
            (string) $stepPayload['csrfHash'],
            $ownedAuth,
        );
        $evidence->assertStatus(201);
        $evidencePayload = $this->json($evidence);
        $this->assertSame((int) $stepPayload['step_id'], (int) $evidencePayload['step_id']);
        $this->assertArrayNotHasKey('database_error', $evidencePayload);
        $this->assertArrayNotHasKey('file', $evidencePayload);
        $this->assertArrayNotHasKey('line', $evidencePayload);
    }

    /** @param array<string, mixed> $data */
    private function jsonPost(string $uri, array $data, string $csrfHash, array $session): TestResponse
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-CSRF-TOKEN' => $csrfHash,
        ])->withBodyFormat('json')
            ->withSession($session)
            ->post($uri, $data);
    }

    private function formPost(string $uri, string $csrfHash, array $session): TestResponse
    {
        $security = service('security');

        return $this->withHeaders(['Accept' => 'application/json'])
            ->withBodyFormat('')
            ->withSession($session)
            ->post($uri, [$security->getTokenName() => $csrfHash]);
    }

    /** @return array<string, mixed> */
    private function json(TestResponse $response): array
    {
        $body = (string) $response->response()->getBody();
        $payload = json_decode($body, true);
        $this->assertIsArray($payload, $body);

        return $payload;
    }

    private function setGateEnvironment(string $diagnosticsEnabled, string $gateEnabled): void
    {
        $values = [
            'METODIKA_DIAGNOSTICS_ENABLED' => $diagnosticsEnabled,
            'METODIKA_DIAGNOSTICS_TOKEN' => 'secret-token',
            'METODIKA_GATE_ENABLED' => $gateEnabled,
        ];

        foreach ($values as $name => $value) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    /** @return array<string, mixed> */
    private function authorizedSession(): array
    {
        return [
            'metodika_diagnostics_auth' => true,
            'metodika_diagnostics_auth_time' => time(),
        ];
    }
}
