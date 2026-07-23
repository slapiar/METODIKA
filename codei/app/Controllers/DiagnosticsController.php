<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DiagnosticsConcurrencyRunStore;
use App\Services\DiagnosticsConcurrencyRunState;
use App\Services\DatabaseCapabilityInspector;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\ExternalEnvironment;
use Config\Services;

final class DiagnosticsController extends BaseController
{
    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';
    private const AUTH_TTL_SECONDS = 900;
    private const CONCURRENCY_RUN_TTL_SECONDS = 180;
    private const BARRIER_WAIT_TIMEOUT_MS = 2500;

    public function database(): ResponseInterface
    {
        if (! $this->isDiagnosticsEnabled()) {
            return $this->diagnosticsFallbackNotFound();
        }

        if ($this->expectedToken() === null) {
            return $this->diagnosticsFallbackNotFound();
        }

        if (! $this->isAuthorized()) {
            return $this->diagnosticsFallbackNotFound();
        }

        $inspection = $this->inspector()->inspect();

        return $this->secureHtmlResponse(view('diagnostics/database', [
            'externalEnvironmentLoaded' => ExternalEnvironment::isLoaded(),
            'inspection' => $inspection,
            'overallReady' => $inspection['connection']
                && $inspection['server']
                && $inspection['innodb']
                && $inspection['utf8mb4Bin']
                && $inspection['datetime6'],
        ]));
    }

    public function login(): ResponseInterface
    {
        if (! $this->isDiagnosticsEnabled()) {
            return $this->diagnosticsFallbackNotFound();
        }

        $expectedToken = $this->expectedToken();
        $submittedToken = $this->request->getPost('diagnostics_token');

        if ($expectedToken === null || ! is_string($submittedToken) || ! hash_equals($expectedToken, $submittedToken)) {
            return $this->diagnosticsFallbackNotFound();
        }

        $session = $this->session();
        $session->regenerate(true);
        $session->set(self::AUTH_SESSION_KEY, true);
        $session->set(self::AUTH_TIME_SESSION_KEY, time());

        return redirect()->to(site_url('diagnostics/database'));
    }

    public function loginForm(): ResponseInterface
    {
        if (! $this->isDiagnosticsEnabled() || $this->expectedToken() === null) {
            return $this->diagnosticsFallbackNotFound();
        }

        if ($this->isAuthorized()) {
            return redirect()->to(site_url('diagnostics/database'));
        }

        return $this->secureHtmlResponse(view('diagnostics/login'));
    }

    public function run(): ResponseInterface
    {
        if (! $this->isDiagnosticsEnabled() || $this->expectedToken() === null || ! $this->isAuthorized()) {
            return $this->diagnosticsFallbackNotFound();
        }

        return redirect()->to(site_url('diagnostics/database'));
    }

    public function startConcurrencyRun(): ResponseInterface
    {
        if (
            ! $this->isDiagnosticsEnabled()
            || ! $this->isConcurrencyWebEnabled()
            || $this->expectedToken() === null
            || ! $this->isAuthorized()
        ) {
            return $this->diagnosticsFallbackNotFound();
        }

        $session = $this->session();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        unset($session);

        $runId = 'run-' . bin2hex(random_bytes(12));
        $participantTokenA = bin2hex(random_bytes(24));
        $participantTokenB = bin2hex(random_bytes(24));

        $requestReference = $this->requestReference();
        $derivationApplicationInput = $this->derivationApplicationInput();
        $derivationReferenceA = 'derivation-a-' . bin2hex(random_bytes(8));
        $derivationReferenceB = 'derivation-b-' . bin2hex(random_bytes(8));
        $payloadFingerprint = hash('sha256', implode('|', [
            $requestReference,
            $derivationReferenceA,
            $derivationReferenceB,
            $derivationApplicationInput,
        ]));

        $now = time();
        $createdAt = gmdate('c', $now);
        $expiresAt = gmdate('c', $now + self::CONCURRENCY_RUN_TTL_SECONDS);

        $document = [
            'version' => 1,
            'runId' => $runId,
            'state' => DiagnosticsConcurrencyRunState::CREATED,
            'createdAt' => $createdAt,
            'expiresAt' => $expiresAt,
            'input' => [
                'requestReference' => $requestReference,
                'payloadFingerprint' => $payloadFingerprint,
                'derivationReferenceA' => $derivationReferenceA,
                'derivationReferenceB' => $derivationReferenceB,
                'derivationApplicationInput' => $derivationApplicationInput,
            ],
            'participants' => [
                'a' => [
                    'tokenHash' => hash('sha256', $participantTokenA),
                    'consumedAt' => null,
                    'readyAt' => null,
                    'startedAt' => null,
                    'finishedAt' => null,
                    'outcome' => null,
                    'errorCode' => null,
                ],
                'b' => [
                    'tokenHash' => hash('sha256', $participantTokenB),
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
                'waitTimeoutMs' => self::BARRIER_WAIT_TIMEOUT_MS,
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

        $this->runStore()->save($runId, $document);

        return $this->secureJsonResponse([
            'runId' => $runId,
            'participantTokenA' => $participantTokenA,
            'participantTokenB' => $participantTokenB,
            'expiresAt' => $expiresAt,
        ]);
    }

    public function logout(): ResponseInterface
    {
        if (! $this->isDiagnosticsEnabled() || $this->expectedToken() === null || ! $this->isAuthorized()) {
            return $this->diagnosticsFallbackNotFound();
        }

        $session = $this->session();
        $session->remove(self::AUTH_SESSION_KEY);
        $session->remove(self::AUTH_TIME_SESSION_KEY);
        $session->regenerate(true);

        return redirect()->to(site_url('diagnostics/database'));
    }

    private function inspector(): DatabaseCapabilityInspector
    {
        /** @var DatabaseCapabilityInspector $inspector */
        $inspector = Services::databaseCapabilityInspector();
        return $inspector;
    }

    private function isDiagnosticsEnabled(): bool
    {
        $enabled = getenv('METODIKA_DIAGNOSTICS_ENABLED');
        return is_string($enabled) && trim($enabled) === '1';
    }

    private function isConcurrencyWebEnabled(): bool
    {
        $enabled = getenv('METODIKA_CONCURRENCY_WEB_ENABLED');
        return is_string($enabled) && trim($enabled) === '1';
    }

    private function expectedToken(): ?string
    {
        $token = getenv('METODIKA_DIAGNOSTICS_TOKEN');
        if (! is_string($token) || trim($token) === '') {
            return null;
        }

        return $token;
    }

    private function isAuthorized(): bool
    {
        $session = $this->session();

        $authorized = $session->get(self::AUTH_SESSION_KEY) === true;
        $authorizedAt = $session->get(self::AUTH_TIME_SESSION_KEY);
        $authorizedAt = is_numeric($authorizedAt) ? (int) $authorizedAt : 0;

        if (! $authorized) {
            return false;
        }

        if (($authorizedAt + self::AUTH_TTL_SECONDS) < time()) {
            $session->remove(self::AUTH_SESSION_KEY);
            $session->remove(self::AUTH_TIME_SESSION_KEY);
            return false;
        }

        return true;
    }

    private function session(): Session
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Strict');

        if ($this->request->isSecure()) {
            ini_set('session.cookie_secure', '1');
        }

        /** @var Session $session */
        $session = session();
        return $session;
    }

    private function diagnosticsFallbackNotFound(): ResponseInterface
    {
        return $this->secureHtmlResponse(view('errors/html/diagnostics_fallback_404'), 404);
    }

    private function runStore(): DiagnosticsConcurrencyRunStore
    {
        /** @var DiagnosticsConcurrencyRunStore $store */
        $store = Services::diagnosticsConcurrencyRunStore();
        return $store;
    }

    private function requestReference(): string
    {
        $requestReference = $this->request->getPost('requestReference');
        if (is_string($requestReference) && trim($requestReference) !== '') {
            return trim($requestReference);
        }

        return 'diag-request-' . bin2hex(random_bytes(8));
    }

    private function derivationApplicationInput(): string
    {
        $input = $this->request->getPost('derivationApplicationInput');
        if (is_string($input) && trim($input) !== '') {
            return trim($input);
        }

        return '{}';
    }

    private function secureHtmlResponse(string $html, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Content-Security-Policy', "default-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; style-src 'self' 'unsafe-inline'")
            ->setContentType('text/html', 'UTF-8')
            ->setBody($html);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function secureJsonResponse(array $payload, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Content-Security-Policy', "default-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; style-src 'self' 'unsafe-inline'")
            ->setContentType('application/json', 'UTF-8')
            ->setJSON($payload);
    }
}
