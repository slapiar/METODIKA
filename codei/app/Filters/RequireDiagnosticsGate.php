<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

final class RequireDiagnosticsGate implements FilterInterface
{
    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';
    private const AUTH_TTL_SECONDS = 900;

    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        if (! $this->flagEnabled('METODIKA_DIAGNOSTICS_ENABLED') || ! $this->flagEnabled('METODIKA_GATE_ENABLED')) {
            return $this->notFound($request);
        }

        $token = getenv('METODIKA_DIAGNOSTICS_TOKEN');
        if (! is_string($token) || trim($token) === '') {
            return $this->notFound($request);
        }

        $session = session();
        $authenticatedAt = $session->get(self::AUTH_TIME_SESSION_KEY);
        $authenticatedAt = is_numeric($authenticatedAt) ? (int) $authenticatedAt : 0;

        if (
            $session->get(self::AUTH_SESSION_KEY) !== true
            || $authenticatedAt <= 0
            || ($authenticatedAt + self::AUTH_TTL_SECONDS) < time()
        ) {
            $session->remove(self::AUTH_SESSION_KEY);
            $session->remove(self::AUTH_TIME_SESSION_KEY);

            return $this->notFound($request);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }

    private function flagEnabled(string $name): bool
    {
        $value = getenv($name);

        return is_string($value) && trim($value) === '1';
    }

    private function notFound(RequestInterface $request): ResponseInterface
    {
        $response = Services::response()
            ->setStatusCode(404)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer');

        $path = ltrim($request->getUri()->getPath(), '/');
        if (str_starts_with($path, 'api/') || str_contains($path, '/create-test-') || str_ends_with($path, '/test-api')) {
            return $response
                ->setHeader('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'")
                ->setJSON([
                    'ok' => false,
                    'errorCode' => 'DIAGNOSTICS_NOT_AVAILABLE',
                ]);
        }

        return $response
            ->setHeader(
                'Content-Security-Policy',
                "default-src 'none'; base-uri 'none'; frame-ancestors 'none'; style-src 'self' 'unsafe-inline'",
            )
            ->setContentType('text/html', 'UTF-8')
            ->setBody(view('errors/html/diagnostics_fallback_404'));
    }
}
