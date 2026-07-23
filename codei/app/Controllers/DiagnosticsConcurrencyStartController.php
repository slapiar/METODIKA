<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DiagnosticsConcurrencyRunState;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Throwable;

final class DiagnosticsConcurrencyStartController extends BaseController
{
    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';
    private const AUTH_TTL_SECONDS = 900;
    private const CONCURRENCY_RUN_TTL_SECONDS = 180;
    private const BARRIER_WAIT_TIMEOUT_MS = 2500;

    public function start(): ResponseInterface
    {
        if (
            ! $this->flagEnabled('METODIKA_DIAGNOSTICS_ENABLED')
            || ! $this->flagEnabled('METODIKA_CONCURRENCY_WEB_ENABLED')
            || $this->expectedToken() === null
            || ! $this->isAuthorized()
        ) {
            return $this->secureJsonResponse([
                'ok' => false,
                'errorCode' => 'DIAGNOSTICS_NOT_AVAILABLE',
                'csrfHash' => csrf_hash(),
            ], 404);
        }

        $session = $this->session();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        unset($session);

        try {
            $runId = 'run-' . bin2hex(random_bytes(12));
            $participantTokenA = bin2hex(random_bytes(24));
            $participantTokenB = bin2hex(random_bytes(24));
            $requestReference = $this->postedString('requestReference', 'diag-request-' . bin2hex(random_bytes(8)));
            $derivationApplicationInput = $this->postedString('derivationApplicationInput', '{}');
            $derivationReferenceA = 'derivation-a-' . bin2hex(random_bytes(8));
            $derivationReferenceB = 'derivation-b-' . bin2hex(random_bytes(8));
            $payloadFingerprint = hash('sha256', implode('|', [
                $requestReference,
                $derivationReferenceA,
                $derivationReferenceB,
                $derivationApplicationInput,
            ]));

            $now = time();
            $expiresAt = gmdate('c', $now + self::CONCURRENCY_RUN_TTL_SECONDS);
            $document = [
                'version' => 1,
                'runId' => $runId,
                'state' => DiagnosticsConcurrencyRunState::CREATED,
                'createdAt' => gmdate('c', $now),
                'expiresAt' => $expiresAt,
                'input' => [
                    'requestReference' => $requestReference,
                    'payloadFingerprint' => $payloadFingerprint,
                    'derivationReferenceA' => $derivationReferenceA,
                    'derivationReferenceB' => $derivationReferenceB,
                    'derivationApplicationInput' => $derivationApplicationInput,
                ],
                'participants' => [
                    'a' => $this->participantDocument($participantTokenA),
                    'b' => $this->participantDocument($participantTokenB),
                ],
                'barrier' => ['openedAt' => null, 'waitTimeoutMs' => self::BARRIER_WAIT_TIMEOUT_MS],
                'finalization' => ['claimedAt' => null, 'claimedBy' => null, 'finishedAt' => null],
                'cleanup' => ['cleanupConfirmed' => false, 'cleanupErrorCode' => null],
                'assertions' => [
                    'dbUniquenessConfirmed' => null,
                    'appReplayConfirmed' => null,
                    'cleanupConfirmed' => null,
                    'overallSuccess' => null,
                ],
            ];

            Services::diagnosticsConcurrencyRunStore()->save($runId, $document);

            return $this->secureJsonResponse([
                'ok' => true,
                'runId' => $runId,
                'participantTokenA' => $participantTokenA,
                'participantTokenB' => $participantTokenB,
                'expiresAt' => $expiresAt,
                'csrfHash' => csrf_hash(),
            ]);
        } catch (Throwable $exception) {
            $errorCode = $this->classifyException($exception);
            log_message('error', 'Diagnostics concurrency START failed [{code}]: {message}', [
                'code' => $errorCode,
                'message' => $exception->getMessage(),
            ]);

            return $this->secureJsonResponse([
                'ok' => false,
                'errorCode' => $errorCode,
                'csrfHash' => csrf_hash(),
            ], 500);
        }
    }

    /** @return array<string, mixed> */
    private function participantDocument(string $token): array
    {
        return [
            'tokenHash' => hash('sha256', $token),
            'consumedAt' => null,
            'readyAt' => null,
            'startedAt' => null,
            'finishedAt' => null,
            'outcome' => null,
            'errorCode' => null,
        ];
    }

    private function classifyException(Throwable $exception): string
    {
        $message = $exception->getMessage();

        return match (true) {
            str_contains($message, 'Adresar pre run store') => 'RUN_STORE_DIRECTORY_CREATE_FAILED',
            str_contains($message, 'Lock subor sa nepodarilo otvorit') => 'RUN_STORE_LOCK_OPEN_FAILED',
            str_contains($message, 'Lock sa nepodarilo ziskat') => 'RUN_STORE_LOCK_ACQUIRE_FAILED',
            str_contains($message, 'Docasny run subor sa nepodarilo otvorit') => 'RUN_STORE_TEMP_OPEN_FAILED',
            str_contains($message, 'Docasny run subor sa nepodarilo zapisat') => 'RUN_STORE_TEMP_WRITE_FAILED',
            str_contains($message, 'Docasny run subor sa nepodarilo flushnut') => 'RUN_STORE_TEMP_FLUSH_FAILED',
            str_contains($message, 'Run dokument sa nepodarilo atomicky nahradit') => 'RUN_STORE_ATOMIC_RENAME_FAILED',
            str_contains($message, 'JSON') || str_contains($message, 'dokument') || str_contains($message, 'prechod') => 'RUN_DOCUMENT_VALIDATION_FAILED',
            default => 'START_INTERNAL_ERROR',
        };
    }

    private function flagEnabled(string $name): bool
    {
        $value = getenv($name);
        return is_string($value) && trim($value) === '1';
    }

    private function expectedToken(): ?string
    {
        $token = getenv('METODIKA_DIAGNOSTICS_TOKEN');
        return is_string($token) && trim($token) !== '' ? $token : null;
    }

    private function postedString(string $name, string $default): string
    {
        $value = $this->request->getPost($name);
        return is_string($value) && trim($value) !== '' ? trim($value) : $default;
    }

    private function isAuthorized(): bool
    {
        $session = $this->session();
        $authorizedAt = $session->get(self::AUTH_TIME_SESSION_KEY);
        $authorizedAt = is_numeric($authorizedAt) ? (int) $authorizedAt : 0;

        return $session->get(self::AUTH_SESSION_KEY) === true
            && ($authorizedAt + self::AUTH_TTL_SECONDS) >= time();
    }

    private function session(): Session
    {
        /** @var Session $session */
        $session = session();
        return $session;
    }

    /** @param array<string, mixed> $payload */
    private function secureJsonResponse(array $payload, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Content-Security-Policy', "default-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'")
            ->setContentType('application/json', 'UTF-8')
            ->setJSON($payload);
    }
}
