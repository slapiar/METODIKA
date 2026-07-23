<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class DiagnosticsConcurrencyRunState
{
    public const CREATED = 'CREATED';
    public const WAITING_FOR_PARTNER = 'WAITING_FOR_PARTNER';
    public const BARRIER_OPEN = 'BARRIER_OPEN';
    public const EXECUTING = 'EXECUTING';
    public const RESULTS_READY = 'RESULTS_READY';
    public const FINALIZATION_CLAIMED = 'FINALIZATION_CLAIMED';
    public const CLEANUP_PENDING = 'CLEANUP_PENDING';
    public const COMPLETED_SUCCESS = 'COMPLETED_SUCCESS';
    public const COMPLETED_FAILED = 'COMPLETED_FAILED';
    public const COMPLETED_FAILED_CLEANUP = 'COMPLETED_FAILED_CLEANUP';
    public const EXPIRED = 'EXPIRED';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::CREATED,
            self::WAITING_FOR_PARTNER,
            self::BARRIER_OPEN,
            self::EXECUTING,
            self::RESULTS_READY,
            self::FINALIZATION_CLAIMED,
            self::CLEANUP_PENDING,
            self::COMPLETED_SUCCESS,
            self::COMPLETED_FAILED,
            self::COMPLETED_FAILED_CLEANUP,
            self::EXPIRED,
        ];
    }

    public static function isValid(string $state): bool
    {
        return in_array($state, self::all(), true);
    }

    public static function assertTransitionAllowed(?string $from, string $to): void
    {
        if (! self::isValid($to)) {
            throw new RuntimeException('Run dokument nie je validny.');
        }

        if ($from === null) {
            if ($to !== self::CREATED) {
                throw new RuntimeException('Run dokument nie je validny.');
            }

            return;
        }

        if (! self::isValid($from)) {
            throw new RuntimeException('Run dokument nie je validny.');
        }

        if ($from === $to) {
            return;
        }

        $allowed = self::allowedTransitions()[$from] ?? [];
        if (! in_array($to, $allowed, true)) {
            throw new RuntimeException('Run dokument nie je validny.');
        }
    }

    /**
     * @return array<string, list<string>>
     */
    private static function allowedTransitions(): array
    {
        return [
            self::CREATED => [self::WAITING_FOR_PARTNER, self::BARRIER_OPEN, self::EXPIRED],
            self::WAITING_FOR_PARTNER => [self::BARRIER_OPEN, self::EXPIRED],
            self::BARRIER_OPEN => [self::EXECUTING, self::EXPIRED],
            self::EXECUTING => [self::RESULTS_READY, self::EXPIRED],
            self::RESULTS_READY => [self::FINALIZATION_CLAIMED],
            self::FINALIZATION_CLAIMED => [self::CLEANUP_PENDING, self::COMPLETED_FAILED, self::COMPLETED_FAILED_CLEANUP],
            self::CLEANUP_PENDING => [self::COMPLETED_SUCCESS, self::COMPLETED_FAILED, self::COMPLETED_FAILED_CLEANUP],
            self::EXPIRED => [self::FINALIZATION_CLAIMED, self::COMPLETED_FAILED, self::COMPLETED_FAILED_CLEANUP],
            self::COMPLETED_SUCCESS => [],
            self::COMPLETED_FAILED => [],
            self::COMPLETED_FAILED_CLEANUP => [],
        ];
    }
}
