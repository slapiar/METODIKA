<?php

declare(strict_types=1);

namespace App\Commands;

use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Application\QuestionDerivation\Data\ReservationResult;
use App\Infrastructure\Persistence\QuestionDerivation\FirstAcceptanceServiceFactory;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\MySQLi\Connection;
use Config\Database;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class ReproduceFirstAcceptanceRootCause extends BaseCommand
{
    protected $group = 'METODIKA';
    protected $name = 'metodika:reproduce-first-acceptance-root-cause';
    protected $description = 'Mimo produkcie reprodukuje nekontrolovaný neúspešný insert rezervácie pri DBDebug=false.';
    protected $usage = 'metodika:reproduce-first-acceptance-root-cause';

    public function run(array $params)
    {
        $dbA = null;
        $dbB = null;
        $requestReference = null;

        try {
            if (ENVIRONMENT === 'production') {
                throw new RuntimeException('Reprodukčný príkaz sa nesmie spustiť v produkčnom prostredí.');
            }

            if (getenv('METODIKA_ROOT_CAUSE_REPRO_ENABLED') !== '1') {
                throw new RuntimeException('Spustenie vyžaduje jednorazový flag METODIKA_ROOT_CAUSE_REPRO_ENABLED=1.');
            }

            $dbConfig = config(Database::class);
            $effectiveConfig = $dbConfig->default;
            $effectiveConfig['pConnect'] = false;
            $effectiveConfig['DBDebug'] = false;

            $dbA = Database::connect($effectiveConfig, false);
            $dbB = Database::connect($effectiveConfig, false);
            $this->assertIndependentMySQLiConnections($dbA, $dbB);

            $token = bin2hex(random_bytes(8));
            $requestReference = 'root-cause-repro-' . $token;
            $runA = $this->makeRun($requestReference, 'a-' . $token);
            $runB = $this->makeRun($requestReference, 'b-' . $token);
            $payloadFingerprint = hash('sha256', 'root-cause-repro-payload-' . $token);

            CLI::write('Fáza A: vytvorenie prvého prijatia cez spojenie A.', 'yellow');
            $resultA = FirstAcceptanceServiceFactory::fromConnection($dbA)->accept($payloadFingerprint, $runA);
            if ($resultA->outcome !== ReservationResult::CREATED) {
                throw new RuntimeException('Spojenie A nevytvorilo prvé prijatie.');
            }

            $this->assertStoredCounts($dbA, $requestReference, 1, 1, 2);

            CLI::write('Fáza B: rovnaká REQUEST_REFERENCE, odlišná derivation_reference, DBDebug=false.', 'yellow');

            $caught = null;
            try {
                FirstAcceptanceServiceFactory::fromConnection($dbB)->accept($payloadFingerprint, $runB);
            } catch (Throwable $exception) {
                $caught = $exception;
            }

            if (! $caught instanceof RuntimeException) {
                $actual = $caught instanceof Throwable ? $caught::class . ': ' . $caught->getMessage() : 'bez výnimky';
                throw new RuntimeException('Koreňová príčina sa nereprodukovala očakávanou RuntimeException; výsledok: ' . $actual);
            }

            $expectedMessage = 'Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.';
            if ($caught->getMessage() !== $expectedMessage) {
                throw new RuntimeException('Vznikla iná RuntimeException: ' . $caught->getMessage(), 0, $caught);
            }

            $this->assertStoredCounts($dbB, $requestReference, 1, 1, 2);

            CLI::newLine();
            CLI::write('ROOT_CAUSE_REPRODUCED', 'green');
            CLI::write('INSERT_FAILURE_PATH=DBDEBUG_FALSE_WITH_UNCHECKED_INSERT_RESULT', 'green');
            CLI::write('POSTCHECK_PATH=REQUEST_REFERENCE_ONLY', 'green');
            CLI::write('FAILURE_PHASE=CREATE_INITIAL_HISTORY_RUN', 'green');
            CLI::write('EXCEPTION_CLASS=' . $caught::class, 'green');
            CLI::write('EXCEPTION_MESSAGE=' . $caught->getMessage(), 'green');
            CLI::write('ROLLBACK_CONFIRMED=SECOND_PARTICIPANT_LEFT_NO_ROWS', 'green');

            return EXIT_SUCCESS;
        } catch (Throwable $exception) {
            CLI::error('Reprodukcia zlyhala: ' . $exception->getMessage());

            return EXIT_ERROR;
        } finally {
            if (is_string($requestReference) && $requestReference !== '') {
                $cleanupDb = $dbA instanceof BaseConnection ? $dbA : ($dbB instanceof BaseConnection ? $dbB : null);
                if ($cleanupDb instanceof BaseConnection) {
                    $this->cleanup($cleanupDb, $requestReference);
                }
            }
        }
    }

    private function assertIndependentMySQLiConnections(BaseConnection $dbA, BaseConnection $dbB): void
    {
        if (! $dbA instanceof Connection || ! $dbB instanceof Connection) {
            throw new RuntimeException('Reprodukcia vyžaduje dve MySQLi spojenia.');
        }

        $dbA->initialize();
        $dbB->initialize();

        if ($dbA->mysqli === $dbB->mysqli || $dbA->mysqli->thread_id === $dbB->mysqli->thread_id) {
            throw new RuntimeException('Databázové spojenia A a B nie sú nezávislé.');
        }
    }

    private function makeRun(string $requestReference, string $suffix): InitialDerivationRun
    {
        return new InitialDerivationRun(
            derivationReference: 'root-cause-derivation-' . $suffix,
            requestReference: $requestReference,
            responseTargetReference: 'root-cause-response',
            requestSourceSnapshot: 'root-cause-source-snapshot',
            sourceQuestionReference: 'root-cause-question',
            derivationSubjectReference: 'root-cause-subject',
            purposeSnapshot: 'root-cause-purpose',
            contextSnapshot: 'root-cause-context',
            scopeSnapshot: 'root-cause-scope',
            domainTermReferences: ['root-cause-domain-term-a', 'root-cause-domain-term-b'],
            actorReference: 'root-cause-actor',
            authorityContextSnapshot: 'root-cause-authority-context',
            runMode: 'PARTIAL_RUN_WITH_ATOMIC_GATE',
            startedAt: new DateTimeImmutable('now'),
        );
    }

    private function assertStoredCounts(
        BaseConnection $db,
        string $requestReference,
        int $expectedReservations,
        int $expectedRuns,
        int $expectedDomainTerms,
    ): void {
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

        if ($reservationCount !== $expectedReservations || $runCount !== $expectedRuns || $domainTermCount !== $expectedDomainTerms) {
            throw new RuntimeException(sprintf(
                'Neočakávané počty: reservations=%d/%d, runs=%d/%d, domain_terms=%d/%d.',
                $reservationCount,
                $expectedReservations,
                $runCount,
                $expectedRuns,
                $domainTermCount,
                $expectedDomainTerms,
            ));
        }
    }

    private function cleanup(BaseConnection $db, string $requestReference): void
    {
        try {
            $db->transRollback();
            $db->resetTransStatus();

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

            $this->assertStoredCounts($db, $requestReference, 0, 0, 0);
            CLI::write('CLEANUP_CONFIRMED', 'green');
        } catch (Throwable $cleanupException) {
            CLI::error('Núdzové čistenie reprodukčných dát zlyhalo: ' . $cleanupException->getMessage());
        }
    }
}
