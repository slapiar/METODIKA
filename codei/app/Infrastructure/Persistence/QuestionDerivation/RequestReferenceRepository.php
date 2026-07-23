<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\QuestionDerivation;

use App\Application\QuestionDerivation\Contracts\RequestReferenceRepositoryPort;
use App\Application\QuestionDerivation\Data\RequestReferenceReservation;
use App\Application\QuestionDerivation\Data\ReservationResult;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Config\Database;
use DateTimeImmutable;
use RuntimeException;

final class RequestReferenceRepository implements RequestReferenceRepositoryPort
{
    private const STATE_RESERVED = 'RESERVED';
    private const STATE_RUNNING = 'RUNNING';
    private const STATE_COMPLETED = 'COMPLETED';

    public function __construct(private readonly BaseConnection $db)
    {
    }

    public static function fromDefaultConnection(): self
    {
        return new self(Database::connect());
    }

    public function reserveFirstAcceptance(
        string $requestReference,
        string $payloadFingerprint,
        string $derivationReference,
        DateTimeImmutable $reservedAt,
    ): ReservationResult {
        $this->assertReference($requestReference, 'request_reference');
        $this->assertReference($derivationReference, 'derivation_reference');

        if (! preg_match('/^[a-f0-9]{64}$/', $payloadFingerprint)) {
            throw new RuntimeException('payload_fingerprint musí byť 64-znakový lowercase SHA-256 odtlačok.');
        }

        $timestamp = $this->formatDateTime($reservedAt);

        try {
            $this->db->table('question_derivation_request_reservations')->insert([
                'request_reference' => $requestReference,
                'payload_fingerprint' => $payloadFingerprint,
                'derivation_reference' => $derivationReference,
                'reservation_state' => self::STATE_RESERVED,
                'reserved_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        } catch (DatabaseException $exception) {
            if ((int) $exception->getCode() !== 1062) {
                throw $exception;
            }

            $existing = $this->findByRequestReference($requestReference);
            if ($existing === null) {
                throw new RuntimeException('Kolízia rezervácie neviedla k nájdeniu existujúcej REQUEST_REFERENCE.', 0, $exception);
            }

            return new ReservationResult(ReservationResult::ALREADY_EXISTS, $existing);
        }

        $created = $this->findByRequestReference($requestReference);
        if ($created === null) {
            throw new RuntimeException('Vytvorenú rezerváciu sa nepodarilo spätne načítať.');
        }

        return new ReservationResult(ReservationResult::CREATED, $created);
    }

    public function findByRequestReference(string $requestReference): ?RequestReferenceReservation
    {
        $this->assertReference($requestReference, 'request_reference');

        $row = $this->db->query(
            <<<'SQL'
SELECT r.request_reference,
       r.payload_fingerprint,
       r.derivation_reference,
       r.reservation_state,
       runs.run_state,
       results.result_reference,
       r.reserved_at,
       r.updated_at
  FROM question_derivation_request_reservations r
  LEFT JOIN question_derivation_runs runs
    ON runs.reservation_id = r.id
  LEFT JOIN question_derivation_run_results results
    ON results.run_id = runs.id
 WHERE r.request_reference = ?
 LIMIT 1
SQL,
            [$requestReference],
        )->getRowArray();

        return is_array($row) ? $this->hydrateReservation($row) : null;
    }

    public function markRunning(
        string $requestReference,
        string $derivationReference,
        DateTimeImmutable $updatedAt,
    ): void {
        $this->updateState(
            $requestReference,
            $derivationReference,
            self::STATE_RUNNING,
            $updatedAt,
        );
    }

    public function attachCompletedResult(
        string $requestReference,
        string $derivationReference,
        string $runState,
        string $resultReference,
        DateTimeImmutable $updatedAt,
    ): void {
        $this->assertReference($resultReference, 'result_reference');

        $correlation = $this->db->query(
            <<<'SQL'
SELECT 1
  FROM question_derivation_request_reservations r
  JOIN question_derivation_runs runs
    ON runs.reservation_id = r.id
   AND runs.derivation_reference = r.derivation_reference
  JOIN question_derivation_run_results results
    ON results.run_id = runs.id
 WHERE r.request_reference = ?
   AND r.derivation_reference = ?
   AND runs.run_state = ?
   AND results.result_reference = ?
   AND results.run_state = ?
 LIMIT 1
SQL,
            [$requestReference, $derivationReference, $runState, $resultReference, $runState],
        )->getRowArray();

        if (! is_array($correlation)) {
            throw new RuntimeException('Finálny výsledok nezodpovedá REQUEST_REFERENCE, derivation_reference alebo run_state.');
        }

        $this->updateState(
            $requestReference,
            $derivationReference,
            self::STATE_COMPLETED,
            $updatedAt,
        );
    }

    public function loadCompletedResult(string $requestReference): ?array
    {
        $this->assertReference($requestReference, 'request_reference');

        $row = $this->db->query(
            <<<'SQL'
SELECT results.*
  FROM question_derivation_request_reservations r
  JOIN question_derivation_runs runs
    ON runs.reservation_id = r.id
   AND runs.derivation_reference = r.derivation_reference
  JOIN question_derivation_run_results results
    ON results.run_id = runs.id
   AND results.request_reference = r.request_reference
 WHERE r.request_reference = ?
   AND r.reservation_state = 'COMPLETED'
 LIMIT 1
SQL,
            [$requestReference],
        )->getRowArray();

        return is_array($row) ? $row : null;
    }

    private function updateState(
        string $requestReference,
        string $derivationReference,
        string $state,
        DateTimeImmutable $updatedAt,
    ): void {
        $this->assertReference($requestReference, 'request_reference');
        $this->assertReference($derivationReference, 'derivation_reference');

        $this->db->table('question_derivation_request_reservations')
            ->where('request_reference', $requestReference)
            ->where('derivation_reference', $derivationReference)
            ->update([
                'reservation_state' => $state,
                'updated_at' => $this->formatDateTime($updatedAt),
            ]);

        if ($this->db->affectedRows() !== 1) {
            throw new RuntimeException('Rezervačný stav nebol zmenený pre presnú koreláciu požiadavky a behu.');
        }
    }

    /** @param array<string, mixed> $row */
    private function hydrateReservation(array $row): RequestReferenceReservation
    {
        return new RequestReferenceReservation(
            requestReference: (string) $row['request_reference'],
            payloadFingerprint: (string) $row['payload_fingerprint'],
            derivationReference: (string) $row['derivation_reference'],
            reservationState: (string) $row['reservation_state'],
            runState: $row['run_state'] !== null ? (string) $row['run_state'] : null,
            resultReference: $row['result_reference'] !== null ? (string) $row['result_reference'] : null,
            reservedAt: new DateTimeImmutable((string) $row['reserved_at']),
            updatedAt: new DateTimeImmutable((string) $row['updated_at']),
        );
    }

    private function assertReference(string $value, string $name): void
    {
        if ($value === '' || mb_strlen($value) > 191) {
            throw new RuntimeException($name . ' musí mať 1 až 191 znakov.');
        }
    }

    private function formatDateTime(DateTimeImmutable $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s.u');
    }
}
