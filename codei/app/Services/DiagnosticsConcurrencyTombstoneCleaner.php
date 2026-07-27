<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class DiagnosticsConcurrencyTombstoneCleaner
{
    public function __construct(private readonly DiagnosticsConcurrencyRunStore $store)
    {
    }

    /** @return array{runId: string, previousState: string, cleanupConfirmed: bool, overallSuccess: bool} */
    public function cleanup(string $runId): array
    {
        $document = $this->store->load($runId);
        if (! is_array($document)) {
            throw new RuntimeException('RUN_NOT_FOUND');
        }

        $state = $document['state'] ?? null;
        if (! is_string($state) || ! in_array($state, [
            DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS,
            DiagnosticsConcurrencyRunState::COMPLETED_FAILED,
            DiagnosticsConcurrencyRunState::COMPLETED_FAILED_CLEANUP,
        ], true)) {
            throw new RuntimeException('RUN_NOT_COMPLETED');
        }

        if (array_key_exists('input', $document)) {
            throw new RuntimeException('RUN_NOT_REDACTED');
        }

        $cleanupConfirmed = ($document['cleanup']['cleanupConfirmed'] ?? null) === true;
        $overallSuccess = ($document['assertions']['overallSuccess'] ?? null) === true;
        if (! $cleanupConfirmed) {
            throw new RuntimeException('DATABASE_CLEANUP_NOT_CONFIRMED');
        }

        if ($state !== DiagnosticsConcurrencyRunState::COMPLETED_SUCCESS && $overallSuccess) {
            throw new RuntimeException('FAILED_RUN_MARKED_SUCCESS');
        }

        $this->store->cleanup($runId);

        $jsonPath = $this->store->baseDirectory() . DIRECTORY_SEPARATOR . $runId . '.json';
        $lockPath = $this->store->baseDirectory() . DIRECTORY_SEPARATOR . $runId . '.lock';
        $tempFiles = glob($jsonPath . '.tmp.*');
        if (is_file($jsonPath) || is_file($lockPath) || (is_array($tempFiles) && $tempFiles !== [])) {
            throw new RuntimeException('RUN_STORE_CLEANUP_POSTCHECK_FAILED');
        }

        return [
            'runId' => $runId,
            'previousState' => $state,
            'cleanupConfirmed' => true,
            'overallSuccess' => $overallSuccess,
        ];
    }
}
