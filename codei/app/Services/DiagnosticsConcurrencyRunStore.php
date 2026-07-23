<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Throwable;

final class DiagnosticsConcurrencyRunStore
{
    private const RUN_ID_PATTERN = '/^[a-z0-9][a-z0-9\-]{7,127}$/';

    private string $baseDirectory;

    private DiagnosticsConcurrencyRunDocumentValidator $validator;

    public function __construct(
        ?string $baseDirectory = null,
        ?DiagnosticsConcurrencyRunDocumentValidator $validator = null,
    ) {
        $this->baseDirectory = rtrim($baseDirectory ?? (WRITEPATH . 'diagnostics/concurrency'), DIRECTORY_SEPARATOR);
        $this->validator = $validator ?? new DiagnosticsConcurrencyRunDocumentValidator();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function load(string $runId): ?array
    {
        return $this->withLock($runId, LOCK_SH, function (string $jsonPath): ?array {
            $document = $this->readDocument($jsonPath);
            if ($document === null) {
                return null;
            }

            $this->validator->validate($document);

            return $document;
        });
    }

    /**
     * @param array<string, mixed> $document
     */
    public function save(string $runId, array $document): void
    {
        $this->withLock($runId, LOCK_EX, function (string $jsonPath) use ($document): void {
            $current = $this->readDocument($jsonPath);

            $this->validator->validate($document);
            $this->validator->validateTransition($current, $document);

            $this->writeJsonAtomically($jsonPath, $document);
        });
    }

    /**
     * @param callable(array<string, mixed>|null): array<string, mixed> $mutator
     * @return array<string, mixed>
     */
    public function mutate(string $runId, callable $mutator): array
    {
        return $this->withLock($runId, LOCK_EX, function (string $jsonPath) use ($mutator): array {
            $current = $this->readDocument($jsonPath);
            if (is_array($current)) {
                $this->validator->validate($current);
            }

            $next = $mutator($current);
            if (! is_array($next)) {
                throw new RuntimeException('Mutator musi vratit JSON objekt ako asociativne pole.');
            }

            if (is_array($current)) {
                $next = $this->preserveOpenBarrierUntilBothParticipantsStarted($current, $next);
            }

            $this->validator->validate($next);
            $this->validator->validateTransition($current, $next);

            $this->writeJsonAtomically($jsonPath, $next);

            return $next;
        });
    }

    public function cleanup(string $runId): void
    {
        $this->assertValidRunId($runId);
        $this->ensureBaseDirectory();

        $jsonPath = $this->jsonPath($runId);
        $lockPath = $this->lockPath($runId);

        $lockHandle = @fopen($lockPath, 'c+b');
        if (! is_resource($lockHandle)) {
            throw new RuntimeException('Lock subor sa nepodarilo otvorit pre cleanup.');
        }

        try {
            if (! @flock($lockHandle, LOCK_EX)) {
                throw new RuntimeException('Lock sa nepodarilo ziskat pre cleanup.');
            }

            $this->deleteIfExists($jsonPath);
            foreach ($this->tempFiles($runId) as $tempPath) {
                $this->deleteIfExists($tempPath);
            }

            @flock($lockHandle, LOCK_UN);
        } finally {
            @fclose($lockHandle);
        }

        $this->deleteIfExists($lockPath);
    }

    public function baseDirectory(): string
    {
        return $this->baseDirectory;
    }

    /**
     * Bariéra je trvalý fakt runu, nie okamžitý medzistav jedného requestu.
     * Stav BARRIER_OPEN preto zostáva zachovaný, kým nezačali obaja účastníci.
     * Čakajúci request tak nemôže prehliadnuť otvorenie bariéry iba preto,
     * že druhý request už stihol prejsť do EXECUTING.
     *
     * @param array<string, mixed> $current
     * @param array<string, mixed> $next
     * @return array<string, mixed>
     */
    private function preserveOpenBarrierUntilBothParticipantsStarted(array $current, array $next): array
    {
        $openedAt = $current['barrier']['openedAt'] ?? $next['barrier']['openedAt'] ?? null;
        $nextState = $next['state'] ?? null;

        if (! is_string($openedAt) || trim($openedAt) === '' || $nextState !== DiagnosticsConcurrencyRunState::EXECUTING) {
            return $next;
        }

        $participants = $next['participants'] ?? null;
        if (! is_array($participants)) {
            return $next;
        }

        foreach (['a', 'b'] as $participant) {
            $startedAt = $participants[$participant]['startedAt'] ?? null;
            if (! is_string($startedAt) || trim($startedAt) === '') {
                $next['state'] = DiagnosticsConcurrencyRunState::BARRIER_OPEN;
                return $next;
            }
        }

        return $next;
    }

    private function assertValidRunId(string $runId): void
    {
        if (preg_match(self::RUN_ID_PATTERN, $runId) !== 1) {
            throw new RuntimeException('Neplatny runId format.');
        }
    }

    private function ensureBaseDirectory(): void
    {
        if (is_dir($this->baseDirectory)) {
            return;
        }

        if (! @mkdir($this->baseDirectory, 0700, true) && ! is_dir($this->baseDirectory)) {
            throw new RuntimeException('Adresar pre run store sa nepodarilo vytvorit.');
        }
    }

    private function lockPath(string $runId): string
    {
        return $this->baseDirectory . DIRECTORY_SEPARATOR . $runId . '.lock';
    }

    private function jsonPath(string $runId): string
    {
        return $this->baseDirectory . DIRECTORY_SEPARATOR . $runId . '.json';
    }

    /**
     * @template T
     * @param callable(string): T $operation
     * @return T
     */
    private function withLock(string $runId, int $lockType, callable $operation): mixed
    {
        $this->assertValidRunId($runId);
        $this->ensureBaseDirectory();

        $lockPath = $this->lockPath($runId);
        $jsonPath = $this->jsonPath($runId);

        $lockHandle = @fopen($lockPath, 'c+b');
        if (! is_resource($lockHandle)) {
            throw new RuntimeException('Lock subor sa nepodarilo otvorit.');
        }

        try {
            if (! @flock($lockHandle, $lockType)) {
                throw new RuntimeException('Lock sa nepodarilo ziskat.');
            }

            $result = $operation($jsonPath);

            @flock($lockHandle, LOCK_UN);

            return $result;
        } catch (Throwable $exception) {
            @flock($lockHandle, LOCK_UN);
            throw $exception;
        } finally {
            @fclose($lockHandle);
        }
    }

    /**
     * @param array<string, mixed> $document
     */
    private function writeJsonAtomically(string $jsonPath, array $document): void
    {
        $encoded = json_encode($document, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (! is_string($encoded)) {
            throw new RuntimeException('Run dokument sa nepodarilo serializovat.');
        }

        $tempPath = sprintf('%s.tmp.%s', $jsonPath, bin2hex(random_bytes(6)));
        $handle = @fopen($tempPath, 'wb');
        if (! is_resource($handle)) {
            throw new RuntimeException('Docasny run subor sa nepodarilo otvorit.');
        }

        try {
            if (@fwrite($handle, $encoded) === false) {
                throw new RuntimeException('Docasny run subor sa nepodarilo zapisat.');
            }

            if (! @fflush($handle)) {
                throw new RuntimeException('Docasny run subor sa nepodarilo flushnut.');
            }
        } finally {
            @fclose($handle);
        }

        if (! @rename($tempPath, $jsonPath)) {
            $this->deleteIfExists($tempPath);
            throw new RuntimeException('Run dokument sa nepodarilo atomicky nahradit.');
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readDocument(string $jsonPath): ?array
    {
        if (! is_file($jsonPath)) {
            return null;
        }

        $raw = @file_get_contents($jsonPath);
        if (! is_string($raw) || $raw === '') {
            throw new RuntimeException('Run dokument sa nepodarilo nacitat.');
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            throw new RuntimeException('Run dokument nie je validny JSON objekt.');
        }

        return $decoded;
    }

    /** @return list<string> */
    private function tempFiles(string $runId): array
    {
        $pattern = $this->jsonPath($runId) . '.tmp.*';
        $matches = glob($pattern);
        if ($matches === false) {
            return [];
        }

        return array_values(array_filter($matches, static fn (mixed $path): bool => is_string($path)));
    }

    private function deleteIfExists(string $path): void
    {
        if (is_file($path) && ! @unlink($path)) {
            throw new RuntimeException('Subor sa nepodarilo odstranit: ' . basename($path));
        }
    }
}
