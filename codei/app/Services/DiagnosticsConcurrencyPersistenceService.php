<?php

declare(strict_types=1);

namespace App\Services;

use Closure;

final class DiagnosticsConcurrencyPersistenceService
{
    /** @var Closure(string): array{reservations:int, runs:int, domainTerms:int} */
    private Closure $counter;

    /** @var Closure(string): void */
    private Closure $cleaner;

    /**
     * @param Closure(string): array{reservations:int, runs:int, domainTerms:int}|null $counter
     * @param Closure(string): void|null $cleaner
     */
    public function __construct(?Closure $counter = null, ?Closure $cleaner = null)
    {
        $this->counter = $counter ?? static function (string $requestReference): array {
            $db = db_connect('default');

            $reservationCount = (int) $db->table('question_derivation_request_reservations')
                ->where('request_reference', $requestReference)
                ->countAllResults();

            $runRows = $db->table('question_derivation_runs')
                ->select('id')
                ->where('request_reference', $requestReference)
                ->get()
                ->getResultArray();

            $runCount = count($runRows);
            $runIds = array_map(static fn (array $row): int => (int) $row['id'], $runRows);
            $domainTermCount = $runIds === []
                ? 0
                : (int) $db->table('question_derivation_run_domain_terms')
                    ->whereIn('run_id', $runIds)
                    ->countAllResults();

            return [
                'reservations' => $reservationCount,
                'runs' => $runCount,
                'domainTerms' => $domainTermCount,
            ];
        };

        $this->cleaner = $cleaner ?? static function (string $requestReference): void {
            $db = db_connect('default');

            $runRows = $db->table('question_derivation_runs')
                ->select('id')
                ->where('request_reference', $requestReference)
                ->get()
                ->getResultArray();
            $runIds = array_map(static fn (array $row): int => (int) $row['id'], $runRows);

            if ($runIds !== []) {
                $db->table('question_derivation_run_domain_terms')->whereIn('run_id', $runIds)->delete();
                $db->table('question_derivation_runs')->whereIn('id', $runIds)->delete();
            }

            $db->table('question_derivation_request_reservations')
                ->where('request_reference', $requestReference)
                ->delete();
        };
    }

    /** @return array{reservations:int, runs:int, domainTerms:int} */
    public function countByRequestReference(string $requestReference): array
    {
        return ($this->counter)($requestReference);
    }

    public function cleanupByRequestReference(string $requestReference): void
    {
        ($this->cleaner)($requestReference);
    }
}
