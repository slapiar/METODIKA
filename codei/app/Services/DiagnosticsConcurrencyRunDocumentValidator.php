<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class DiagnosticsConcurrencyRunDocumentValidator
{
    /**
     * @param array<string, mixed> $document
     */
    public function validate(array $document): void
    {
        $this->requireInt($document, 'version');
        $this->requireString($document, 'runId');
        $state = $this->requireState($document, 'state');
        $this->requireString($document, 'createdAt');
        $this->requireString($document, 'expiresAt');

        $participants = $this->requireArray($document, 'participants');
        $participantA = $this->requireArray($participants, 'a');
        $participantB = $this->requireArray($participants, 'b');

        $this->validateParticipant($participantA);
        $this->validateParticipant($participantB);

        $barrier = $this->requireArray($document, 'barrier');
        $this->requireNullableString($barrier, 'openedAt');
        $waitTimeoutMs = $this->requireInt($barrier, 'waitTimeoutMs');
        if ($waitTimeoutMs < 1) {
            $this->invalid();
        }

        $finalization = $this->requireArray($document, 'finalization');
        $this->requireNullableString($finalization, 'claimedAt');
        $this->requireNullableString($finalization, 'claimedBy');
        $this->requireNullableString($finalization, 'finishedAt');

        $cleanup = $this->requireArray($document, 'cleanup');
        $this->requireBool($cleanup, 'cleanupConfirmed');
        $this->requireNullableString($cleanup, 'cleanupErrorCode');

        $assertions = $this->requireArray($document, 'assertions');
        $this->requireNullableBool($assertions, 'dbUniquenessConfirmed');
        $this->requireNullableBool($assertions, 'appReplayConfirmed');
        $this->requireNullableBool($assertions, 'cleanupConfirmed');
        $this->requireNullableBool($assertions, 'overallSuccess');

        if ($state === DiagnosticsConcurrencyRunState::RESULTS_READY) {
            $this->assertParticipantFinished($participantA);
            $this->assertParticipantFinished($participantB);
        }
    }

    /**
     * @param array<string, mixed>|null $current
     * @param array<string, mixed> $next
     */
    public function validateTransition(?array $current, array $next): void
    {
        $fromState = null;
        if (is_array($current)) {
            $fromState = $this->requireState($current, 'state');

            $currentRunId = $this->requireString($current, 'runId');
            $nextRunId = $this->requireString($next, 'runId');
            if ($currentRunId !== $nextRunId) {
                $this->invalid();
            }
        }

        $toState = $this->requireState($next, 'state');
        DiagnosticsConcurrencyRunState::assertTransitionAllowed($fromState, $toState);
    }

    /**
     * @param array<string, mixed> $participant
     */
    private function validateParticipant(array $participant): void
    {
        $this->requireString($participant, 'tokenHash');
        $this->requireNullableString($participant, 'consumedAt');
        $this->requireNullableString($participant, 'readyAt');
        $this->requireNullableString($participant, 'startedAt');
        $this->requireNullableString($participant, 'finishedAt');
        $this->requireNullableString($participant, 'outcome');
        $this->requireNullableString($participant, 'errorCode');
    }

    /**
     * @param array<string, mixed> $participant
     */
    private function assertParticipantFinished(array $participant): void
    {
        if (! is_string($participant['finishedAt']) || trim($participant['finishedAt']) === '') {
            $this->invalid();
        }

        if (! is_string($participant['outcome']) || trim($participant['outcome']) === '') {
            $this->invalid();
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function requireArray(array $data, string $key): array
    {
        if (! array_key_exists($key, $data) || ! is_array($data[$key])) {
            $this->invalid();
        }

        /** @var array<string, mixed> $value */
        $value = $data[$key];
        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireString(array $data, string $key): string
    {
        if (! array_key_exists($key, $data) || ! is_string($data[$key]) || trim($data[$key]) === '') {
            $this->invalid();
        }

        return $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireNullableString(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data)) {
            $this->invalid();
        }

        $value = $data[$key];
        if ($value === null) {
            return null;
        }

        if (! is_string($value) || trim($value) === '') {
            $this->invalid();
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireInt(array $data, string $key): int
    {
        if (! array_key_exists($key, $data) || ! is_int($data[$key])) {
            $this->invalid();
        }

        return $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireBool(array $data, string $key): bool
    {
        if (! array_key_exists($key, $data) || ! is_bool($data[$key])) {
            $this->invalid();
        }

        return $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireNullableBool(array $data, string $key): ?bool
    {
        if (! array_key_exists($key, $data)) {
            $this->invalid();
        }

        $value = $data[$key];
        if ($value === null) {
            return null;
        }

        if (! is_bool($value)) {
            $this->invalid();
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireState(array $data, string $key): string
    {
        $value = $this->requireString($data, $key);
        if (! DiagnosticsConcurrencyRunState::isValid($value)) {
            $this->invalid();
        }

        return $value;
    }

    private function invalid(): never
    {
        throw new RuntimeException('Run dokument nie je validny.');
    }
}
