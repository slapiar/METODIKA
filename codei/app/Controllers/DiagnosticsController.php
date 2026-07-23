<?php

declare(strict_types=1);

namespace App\Controllers;

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

    public function run(): ResponseInterface
    {
        if (! $this->isDiagnosticsEnabled() || $this->expectedToken() === null || ! $this->isAuthorized()) {
            return $this->diagnosticsFallbackNotFound();
        }

        return redirect()->to(site_url('diagnostics/database'));
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
}
