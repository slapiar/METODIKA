<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Services\DiagnosticsConcurrencyAcceptanceRunner;
use App\Services\DiagnosticsConcurrencyPersistenceService;
use App\Services\DiagnosticsConcurrencyRunStore;
use App\Services\DiagnosticsConcurrencyRunState;
use App\Services\DatabaseCapabilityInspector;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\ExternalEnvironment;
use Config\Services;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class DiagnosticsController extends BaseController
{
    private const AUTH_SESSION_KEY = 'metodika_diagnostics_auth';
    private const AUTH_TIME_SESSION_KEY = 'metodika_diagnostics_auth_time';
    private const AUTH_TTL_SECONDS = 900;
    private const CONCURRENCY_RUN_TTL_SECONDS = 180;
    private const CONCURRENCY_TOMBSTONE_TTL_SECONDS = 600;
    private const BARRIER_WAIT_TIMEOUT_MS = 2500;
    private const HIT_ALLOWED_STATES = [
        DiagnosticsConcurrencyRunState::CREATED,
        DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER,
    ];
    private const HIT_POLL_SLEEP_US = 20_000;

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
        $concurrencyWebEnabled = $this->isConcurrencyWebEnabled();
        $scriptNonce = $concurrencyWebEnabled ? base64_encode(random_bytes(16)) : null;

        return $this->secureHtmlResponse(view('diagnostics/database', [
            'externalEnvironmentLoaded' => ExternalEnvironment::isLoaded(),
            'inspection' => $inspection,
            'overallReady' => $inspection['connection']
                && $inspection['server']
                && $inspection['innodb']
                && $inspection['utf8mb4Bin']
                && $inspection['datetime6'],
            'concurrencyWebEnabled' => $concurrencyWebEnabled,
            'scriptNonce' => $scriptNonce,
        ]), 200, $scriptNonce);
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

    public function hitConcurrencyA(): ResponseInterface
    {
        return $this->handleHit('a');
    }

    public function hitConcurrencyB(): ResponseInterface
    {
        return $this->handleHit('b');
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

    public function concurrencyResult(string $runId): ResponseInterface
    {
        if (
            ! $this->isDiagnosticsEnabled()
            || ! $this->isConcurrencyWebEnabled()
            || $this->expectedToken() === null
            || ! $this->isAuthorized()
        ) {
            return $this->diagnosticsFallbackNotFound();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        try {
            $document = $this->runStore()->load($runId);
            if (! is_array($document)) {
                return $this->diagnosticsFallbackNotFound();
            }

            $deleteAfter = $document['deleteAfter'] ?? null;
            if (is_string($deleteAfter) && strtotime($deleteAfter) !== false && strtotime($deleteAfter) <= time()) {
                $this->runStore()->cleanup($runId);
                return $this->diagnosticsFallbackNotFound();
            }

            $state = $document['state'] ?? null;
            if (in_array($state, [
                DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS,
                DiagnosticsConcurrencyRunState::COMPLETED_FAILED,
                DiagnosticsConcurrencyRunState::COMPLETED_FAILED_CLEANUP,
            ], true)) {
                $document = $this->runStore()->mutate($runId, static function (?array $current): array {
                    if (! is_array($current)) {
                        throw new RuntimeException('Run nenajdeny.');
                    }

                    if (! is_string($current['readOnceConsumedAt'] ?? null) || trim((string) $current['readOnceConsumedAt']) === '') {
                        $current['readOnceConsumedAt'] = gmdate('c');
                    }

                    return $current;
                });
            }
        } catch (Throwable) {
            return $this->diagnosticsFallbackNotFound();
        }

        return $this->secureJsonResponse($this->publicResultPayload($document));
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

    private function handleHit(string $participant): ResponseInterface
    {
        if (
            ! $this->isDiagnosticsEnabled()
            || ! $this->isConcurrencyWebEnabled()
            || $this->expectedToken() === null
            || ! $this->isAuthorized()
        ) {
            return $this->diagnosticsFallbackNotFound();
        }

        $runId = $this->request->getPost('runId');
        $participantToken = $this->request->getPost('participantToken');

        if (! is_string($runId) || trim($runId) === '' || ! is_string($participantToken) || trim($participantToken) === '') {
            return $this->diagnosticsFallbackNotFound();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $nowIso = gmdate('c');
        $tokenHash = hash('sha256', trim($participantToken));

        try {
            $updated = $this->runStore()->mutate(trim($runId), function (?array $current) use ($participant, $tokenHash, $nowIso): array {
                if (! is_array($current)) {
                    throw new RuntimeException('Run nenajdeny.');
                }

                $state = $current['state'] ?? null;
                if (! is_string($state) || ! in_array($state, self::HIT_ALLOWED_STATES, true)) {
                    throw new RuntimeException('Nepovoleny stav runu pre HIT.');
                }

                $slot = $current['participants'][$participant] ?? null;
                if (! is_array($slot)) {
                    throw new RuntimeException('Neplatny participant slot.');
                }

                $storedHash = $slot['tokenHash'] ?? null;
                if (! is_string($storedHash) || ! hash_equals($storedHash, $tokenHash)) {
                    throw new RuntimeException('Neplatny participant token.');
                }

                if (($slot['consumedAt'] ?? null) !== null) {
                    throw new RuntimeException('Participant token uz bol pouzity.');
                }

                $expiresAt = $current['expiresAt'] ?? null;
                if (! is_string($expiresAt) || strtotime($expiresAt) === false || strtotime($expiresAt) < time()) {
                    throw new RuntimeException('Run expiroval.');
                }

                $current['participants'][$participant]['consumedAt'] = $nowIso;
                $current['participants'][$participant]['readyAt'] = $nowIso;

                $partner = $participant === 'a' ? 'b' : 'a';
                $partnerReadyAt = $current['participants'][$partner]['readyAt'] ?? null;

                if (is_string($partnerReadyAt) && $partnerReadyAt !== '') {
                    $current['barrier']['openedAt'] ??= $nowIso;
                    $current['state'] = DiagnosticsConcurrencyRunState::BARRIER_OPEN;
                } else {
                    $current['state'] = DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER;
                }

                return $current;
            });
        } catch (Throwable) {
            return $this->diagnosticsFallbackNotFound();
        }

        $updated = $this->awaitBarrierOrTimeout(trim($runId), $participant, $updated);
        $updated = $this->executeAcceptIfReady(trim($runId), $participant, $updated);
        $updated = $this->tryFinalizationClaim(trim($runId), $participant, $updated);
        $updated = $this->runFinalizationInvariantAndCleanup(trim($runId), $participant, $updated);

        $barrierOpenedAt = $updated['barrier']['openedAt'] ?? null;
        $participantSlot = $updated['participants'][$participant] ?? [];
        $timeoutReached = ($participantSlot['errorCode'] ?? null) === 'PARTNER_TIMEOUT';
        $claimedBy = $updated['finalization']['claimedBy'] ?? null;
        $waiterMode = is_string($claimedBy) && $claimedBy !== '' && $claimedBy !== $participant;

        return $this->secureJsonResponse([
            'runId' => $updated['runId'] ?? trim($runId),
            'participant' => $participant,
            'state' => $updated['state'] ?? null,
            'barrierOpened' => is_string($barrierOpenedAt) && $barrierOpenedAt !== '',
            'timeoutReached' => $timeoutReached,
            'waiterMode' => $waiterMode,
        ]);
    }

    /**
     * @param array<string, mixed> $current
     * @return array<string, mixed>
     */
    private function awaitBarrierOrTimeout(string $runId, string $participant, array $current): array
    {
        $state = $current['state'] ?? null;
        if (! is_string($state) || $state !== DiagnosticsConcurrencyRunState::WAITING_FOR_PARTNER) {
            return $current;
        }

        $timeoutMs = $current['barrier']['waitTimeoutMs'] ?? self::BARRIER_WAIT_TIMEOUT_MS;
        if (! is_int($timeoutMs) || $timeoutMs < 1) {
            $timeoutMs = self::BARRIER_WAIT_TIMEOUT_MS;
        }

        $deadline = microtime(true) + ($timeoutMs / 1000);

        while (microtime(true) < $deadline) {
            $latest = $this->runStore()->load($runId);
            if (! is_array($latest)) {
                return $current;
            }

            $latestState = $latest['state'] ?? null;
            if ($latestState === DiagnosticsConcurrencyRunState::BARRIER_OPEN) {
                return $latest;
            }

            usleep(self::HIT_POLL_SLEEP_US);
        }

        return $this->claimFinalizationForTimeout($runId, $participant);
    }

    /**
     * @return array<string, mixed>
     */
    private function claimFinalizationForTimeout(string $runId, string $participant): array
    {
        return $this->runStore()->mutate($runId, function (?array $current) use ($participant): array {
            if (! is_array($current)) {
                throw new RuntimeException('Run nenajdeny.');
            }

            $state = $current['state'] ?? null;
            if ($state === DiagnosticsConcurrencyRunState::BARRIER_OPEN) {
                return $current;
            }

            $claimedAt = $current['finalization']['claimedAt'] ?? null;
            if (! is_string($claimedAt) || $claimedAt === '') {
                $nowIso = gmdate('c');
                $current['finalization']['claimedAt'] = $nowIso;
                $current['finalization']['claimedBy'] = $participant;
                $current['participants'][$participant]['errorCode'] = 'PARTNER_TIMEOUT';
                $current['participants'][$participant]['outcome'] = 'TIMEOUT';
                $current['participants'][$participant]['finishedAt'] = $nowIso;
                $current['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;
            } else {
                $waiters = $current['finalization']['waiters'] ?? [];
                if (! is_array($waiters)) {
                    $waiters = [];
                }

                $waiters[$participant] = gmdate('c');
                $current['finalization']['waiters'] = $waiters;
                $current['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;
            }

            return $current;
        });
    }

    /**
     * @param array<string, mixed> $current
     * @return array<string, mixed>
     */
    private function tryFinalizationClaim(string $runId, string $participant, array $current): array
    {
        $state = $current['state'] ?? null;
        if (! in_array($state, [DiagnosticsConcurrencyRunState::RESULTS_READY, DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED], true)) {
            return $current;
        }

        return $this->runStore()->mutate($runId, function (?array $latest) use ($participant): array {
            if (! is_array($latest)) {
                throw new RuntimeException('Run nenajdeny.');
            }

            $latestState = $latest['state'] ?? null;
            if (! in_array($latestState, [DiagnosticsConcurrencyRunState::RESULTS_READY, DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED], true)) {
                return $latest;
            }

            $claimedAt = $latest['finalization']['claimedAt'] ?? null;
            $claimedBy = $latest['finalization']['claimedBy'] ?? null;

            if (! is_string($claimedAt) || $claimedAt === '' || ! is_string($claimedBy) || $claimedBy === '') {
                $latest['finalization']['claimedAt'] = gmdate('c');
                $latest['finalization']['claimedBy'] = $participant;
                $latest['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;

                return $latest;
            }

            if ($claimedBy !== $participant) {
                $waiters = $latest['finalization']['waiters'] ?? [];
                if (! is_array($waiters)) {
                    $waiters = [];
                }

                $waiters[$participant] = gmdate('c');
                $latest['finalization']['waiters'] = $waiters;
                $latest['state'] = DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED;
            }

            return $latest;
        });
    }

    /**
     * @param array<string, mixed> $current
     * @return array<string, mixed>
     */
    private function runFinalizationInvariantAndCleanup(string $runId, string $participant, array $current): array
    {
        $state = $current['state'] ?? null;
        if ($state !== DiagnosticsConcurrencyRunState::FINALIZATION_CLAIMED) {
            return $current;
        }

        $claimedBy = $current['finalization']['claimedBy'] ?? null;
        if (! is_string($claimedBy) || $claimedBy !== $participant) {
            return $current;
        }

        $requestReference = $this->requestReferenceFromDocument($current);
        $appReplayConfirmed = $this->isReplayOutcomePairValid($current);

        $dbUniquenessConfirmed = false;
        $cleanupConfirmed = false;
        $cleanupErrorCode = null;

        try {
            $before = $this->persistenceService()->countByRequestReference($requestReference);
            $dbUniquenessConfirmed = $before['reservations'] === 1;

            $this->runStore()->mutate($runId, function (?array $latest) use ($dbUniquenessConfirmed, $appReplayConfirmed): array {
                if (! is_array($latest)) {
                    throw new RuntimeException('Run nenajdeny.');
                }

                $latest['assertions']['dbUniquenessConfirmed'] = $dbUniquenessConfirmed;
                $latest['assertions']['appReplayConfirmed'] = $appReplayConfirmed;
                $latest['assertions']['cleanupConfirmed'] = null;
                $latest['assertions']['overallSuccess'] = null;
                $latest['state'] = DiagnosticsConcurrencyRunState::CLEANUP_PENDING;

                return $latest;
            });

            $this->persistenceService()->cleanupByRequestReference($requestReference);
            $after = $this->persistenceService()->countByRequestReference($requestReference);

            $cleanupConfirmed = $after['reservations'] === 0
                && $after['runs'] === 0
                && $after['domainTerms'] === 0;

            if (! $cleanupConfirmed) {
                $cleanupErrorCode = 'CLEANUP_POSTCHECK_FAILED';
            }
        } catch (Throwable) {
            $cleanupErrorCode = 'CLEANUP_FAILED';
        }

        $overallSuccess = $dbUniquenessConfirmed && $appReplayConfirmed && $cleanupConfirmed;
        $completedState = $overallSuccess
            ? DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS
            : ($cleanupConfirmed ? DiagnosticsConcurrencyRunState::COMPLETED_FAILED : DiagnosticsConcurrencyRunState::COMPLETED_FAILED_CLEANUP);

        return $this->runStore()->mutate($runId, function (?array $latest) use (
            $dbUniquenessConfirmed,
            $appReplayConfirmed,
            $cleanupConfirmed,
            $cleanupErrorCode,
            $overallSuccess,
            $completedState,
            $participant,
        ): array {
            if (! is_array($latest)) {
                throw new RuntimeException('Run nenajdeny.');
            }

            $latest['assertions']['dbUniquenessConfirmed'] = $dbUniquenessConfirmed;
            $latest['assertions']['appReplayConfirmed'] = $appReplayConfirmed;
            $latest['assertions']['cleanupConfirmed'] = $cleanupConfirmed;
            $latest['assertions']['overallSuccess'] = $overallSuccess;

            $latest['cleanup']['cleanupConfirmed'] = $cleanupConfirmed;
            $latest['cleanup']['cleanupErrorCode'] = $cleanupErrorCode;

            $latest['finalization']['finishedAt'] = gmdate('c');
            $latest['finalization']['claimedBy'] = $participant;
            $latest['state'] = $completedState;

            $latest = $this->reduceToTombstone($latest);

            return $latest;
        });
    }

    /**
     * @param array<string, mixed> $current
     * @return array<string, mixed>
     */
    private function executeAcceptIfReady(string $runId, string $participant, array $current): array
    {
        $state = $current['state'] ?? null;
        if (! in_array($state, [DiagnosticsConcurrencyRunState::BARRIER_OPEN, DiagnosticsConcurrencyRunState::EXECUTING], true)) {
            return $current;
        }

        $marked = $this->runStore()->mutate($runId, function (?array $latest) use ($participant): array {
            if (! is_array($latest)) {
                throw new RuntimeException('Run nenajdeny.');
            }

            $slot = $latest['participants'][$participant] ?? null;
            if (! is_array($slot)) {
                throw new RuntimeException('Neplatny participant slot.');
            }

            if (is_string($slot['finishedAt'] ?? null) && trim((string) $slot['finishedAt']) !== '') {
                return $latest;
            }

            if (! is_string($slot['startedAt'] ?? null) || trim((string) $slot['startedAt']) === '') {
                $latest['participants'][$participant]['startedAt'] = gmdate('c');
            }

            $latest['state'] = DiagnosticsConcurrencyRunState::EXECUTING;

            return $latest;
        });

        $slot = $marked['participants'][$participant] ?? [];
        if (is_string($slot['finishedAt'] ?? null) && trim((string) $slot['finishedAt']) !== '') {
            return $marked;
        }

        $outcome = 'FAILED';
        $errorCode = null;

        try {
            $run = $this->buildInitialRunFromDocument($marked, $participant);
            $payloadFingerprint = $this->payloadFingerprintFromDocument($marked);
            $outcome = $this->acceptanceRunner()->accept($payloadFingerprint, $run);
        } catch (Throwable) {
            $errorCode = 'ACCEPT_RUNTIME_ERROR';
        }

        $completedAt = gmdate('c');

        return $this->runStore()->mutate($runId, function (?array $latest) use ($participant, $outcome, $errorCode, $completedAt): array {
            if (! is_array($latest)) {
                throw new RuntimeException('Run nenajdeny.');
            }

            $slot = $latest['participants'][$participant] ?? null;
            if (! is_array($slot)) {
                throw new RuntimeException('Neplatny participant slot.');
            }

            if (is_string($slot['finishedAt'] ?? null) && trim((string) $slot['finishedAt']) !== '') {
                return $latest;
            }

            $latest['participants'][$participant]['finishedAt'] = $completedAt;
            $latest['participants'][$participant]['outcome'] = $outcome;
            $latest['participants'][$participant]['errorCode'] = $errorCode;

            $otherParticipant = $participant === 'a' ? 'b' : 'a';
            $otherSlot = $latest['participants'][$otherParticipant] ?? null;

            $otherFinished = is_array($otherSlot)
                && is_string($otherSlot['finishedAt'] ?? null)
                && trim((string) $otherSlot['finishedAt']) !== '';
            $otherOutcome = is_array($otherSlot)
                && is_string($otherSlot['outcome'] ?? null)
                && trim((string) $otherSlot['outcome']) !== '';

            if ($otherFinished && $otherOutcome) {
                $latest['state'] = DiagnosticsConcurrencyRunState::RESULTS_READY;
            } else {
                $latest['state'] = DiagnosticsConcurrencyRunState::EXECUTING;
            }

            return $latest;
        });
    }

    private function acceptanceRunner(): DiagnosticsConcurrencyAcceptanceRunner
    {
        /** @var DiagnosticsConcurrencyAcceptanceRunner $runner */
        $runner = Services::diagnosticsConcurrencyAcceptanceRunner();
        return $runner;
    }

    private function persistenceService(): DiagnosticsConcurrencyPersistenceService
    {
        /** @var DiagnosticsConcurrencyPersistenceService $service */
        $service = Services::diagnosticsConcurrencyPersistenceService();
        return $service;
    }

    /**
     * @param array<string, mixed> $document
     */
    private function payloadFingerprintFromDocument(array $document): string
    {
        $input = $document['input'] ?? null;
        if (! is_array($input)) {
            throw new RuntimeException('Run input chyba.');
        }

        $payloadFingerprint = $input['payloadFingerprint'] ?? null;
        if (! is_string($payloadFingerprint) || trim($payloadFingerprint) === '') {
            throw new RuntimeException('payloadFingerprint chyba.');
        }

        return $payloadFingerprint;
    }

    /**
     * @param array<string, mixed> $document
     */
    private function buildInitialRunFromDocument(array $document, string $participant): InitialDerivationRun
    {
        $input = $document['input'] ?? null;
        if (! is_array($input)) {
            throw new RuntimeException('Run input chyba.');
        }

        $requestReference = $this->requiredDocumentString($input, 'requestReference');
        $applicationInput = $this->requiredDocumentString($input, 'derivationApplicationInput');

        $derivationReferenceKey = $participant === 'a' ? 'derivationReferenceA' : 'derivationReferenceB';
        $derivationReference = $this->requiredDocumentString($input, $derivationReferenceKey);

        return new InitialDerivationRun(
            derivationReference: $derivationReference,
            requestReference: $requestReference,
            responseTargetReference: 'diagnostics-concurrency-response',
            requestSourceSnapshot: $applicationInput,
            sourceQuestionReference: 'diagnostics-concurrency-question',
            derivationSubjectReference: 'diagnostics-concurrency-subject',
            purposeSnapshot: $applicationInput,
            contextSnapshot: $applicationInput,
            scopeSnapshot: $applicationInput,
            domainTermReferences: ['diagnostics-concurrency-term-a', 'diagnostics-concurrency-term-b'],
            actorReference: 'diagnostics-concurrency-actor',
            authorityContextSnapshot: 'diagnostics-concurrency-authority',
            runMode: 'PARTIAL_RUN_WITH_ATOMIC_GATE',
            startedAt: new DateTimeImmutable('now'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requiredDocumentString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        if (! is_string($value) || trim($value) === '') {
            throw new RuntimeException('Run dokument neobsahuje pole ' . $key . '.');
        }

        return trim($value);
    }

    /**
     * @param array<string, mixed> $document
     */
    private function requestReferenceFromDocument(array $document): string
    {
        $input = $document['input'] ?? null;
        if (! is_array($input)) {
            throw new RuntimeException('Run input chyba.');
        }

        return $this->requiredDocumentString($input, 'requestReference');
    }

    /**
     * @param array<string, mixed> $document
     */
    private function isReplayOutcomePairValid(array $document): bool
    {
        $participants = $document['participants'] ?? null;
        if (! is_array($participants)) {
            return false;
        }

        $slotA = $participants['a'] ?? null;
        $slotB = $participants['b'] ?? null;
        if (! is_array($slotA) || ! is_array($slotB)) {
            return false;
        }

        $outcomeA = $slotA['outcome'] ?? null;
        $outcomeB = $slotB['outcome'] ?? null;
        if (! is_string($outcomeA) || ! is_string($outcomeB)) {
            return false;
        }

        $pair = [$outcomeA, $outcomeB];
        sort($pair);

        return $pair === ['ALREADY_EXISTS', 'CREATED'];
    }

    /**
     * @param array<string, mixed> $document
     * @return array<string, mixed>
     */
    private function reduceToTombstone(array $document): array
    {
        $completedAt = gmdate('c');
        $deleteAfter = gmdate('c', time() + self::CONCURRENCY_TOMBSTONE_TTL_SECONDS);

        $participants = $document['participants'] ?? [];
        if (! is_array($participants)) {
            $participants = [];
        }

        $sanitize = static function (mixed $slot): array {
            if (! is_array($slot)) {
                $slot = [];
            }

            return [
                'tokenHash' => null,
                'consumedAt' => null,
                'readyAt' => null,
                'startedAt' => is_string($slot['startedAt'] ?? null) ? $slot['startedAt'] : null,
                'finishedAt' => is_string($slot['finishedAt'] ?? null) ? $slot['finishedAt'] : null,
                'outcome' => is_string($slot['outcome'] ?? null) ? $slot['outcome'] : null,
                'errorCode' => is_string($slot['errorCode'] ?? null) ? $slot['errorCode'] : null,
            ];
        };

        $document['participants'] = [
            'a' => $sanitize($participants['a'] ?? []),
            'b' => $sanitize($participants['b'] ?? []),
        ];

        unset($document['input']);

        $document['completedAt'] = $completedAt;
        $document['deleteAfter'] = $deleteAfter;
        $document['readOnceConsumedAt'] = null;

        return $document;
    }

    /**
     * @param array<string, mixed> $document
     * @return array<string, mixed>
     */
    private function publicResultPayload(array $document): array
    {
        $participants = $document['participants'] ?? [];
        if (! is_array($participants)) {
            $participants = [];
        }

        $participantPayload = static function (mixed $slot): array {
            if (! is_array($slot)) {
                $slot = [];
            }

            return [
                'startedAt' => $slot['startedAt'] ?? null,
                'finishedAt' => $slot['finishedAt'] ?? null,
                'outcome' => $slot['outcome'] ?? null,
                'errorCode' => $slot['errorCode'] ?? null,
            ];
        };

        return [
            'runId' => $document['runId'] ?? null,
            'state' => $document['state'] ?? null,
            'completedAt' => $document['completedAt'] ?? null,
            'deleteAfter' => $document['deleteAfter'] ?? null,
            'readOnceConsumedAt' => $document['readOnceConsumedAt'] ?? null,
            'assertions' => $document['assertions'] ?? null,
            'cleanup' => $document['cleanup'] ?? null,
            'participants' => [
                'a' => $participantPayload($participants['a'] ?? []),
                'b' => $participantPayload($participants['b'] ?? []),
            ],
        ];
    }

    private function secureHtmlResponse(string $html, int $statusCode = 200, ?string $scriptNonce = null): ResponseInterface
    {
        $csp = "default-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; style-src 'self' 'unsafe-inline'";
        if (is_string($scriptNonce) && trim($scriptNonce) !== '') {
            $csp .= "; script-src 'nonce-" . $scriptNonce . "'";
        }

        return $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('X-Frame-Options', 'DENY')
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Content-Security-Policy', $csp)
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
