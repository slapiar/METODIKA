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
use mysqli;
use mysqli_sql_exception;
use RuntimeException;
use Throwable;

final class VerifyConcurrentFirstAcceptance extends BaseCommand
{
    protected $group = 'METODIKA';
    protected $name = 'metodika:verify-concurrent-first-acceptance';
    protected $description = 'Bez trvalých dát overí kolíziu rovnakej REQUEST_REFERENCE cez dve samostatné MySQLi spojenia.';
    protected $usage = 'metodika:verify-concurrent-first-acceptance';

    public function run(array $params)
    {
        $dbA = null;
        $dbB = null;
        $requestReference = null;

        try {
            if (! defined('MYSQLI_ASYNC')) {
                throw new RuntimeException('Aktuálny MySQLi runtime nepodporuje MYSQLI_ASYNC.');
            }

            $dbA = Database::connect(null, false);
            $dbB = Database::connect(null, false);
            $this->assertIndependentMySQLiConnections($dbA, $dbB);

            $token = bin2hex(random_bytes(8));
            $requestReference = 'concurrent-request-' . $token;
            $runA = $this->makeRun($requestReference, 'a-' . $token);
            $runB = $this->makeRun($requestReference, 'b-' . $token);
            $payloadFingerprint = hash('sha256', $runA->requestSourceSnapshot);

            CLI::write('Spojenie A: vytvorenie prvého prijatia v otvorenej vonkajšej transakcii.', 'yellow');
            if (! $dbA->transBegin()) {
                throw new RuntimeException('Vonkajšiu transakciu spojenia A sa nepodarilo začať.');
            }

            $resultA = FirstAcceptanceServiceFactory::fromConnection($dbA)->accept($payloadFingerprint, $runA);
            if ($resultA->outcome !== ReservationResult::CREATED) {
                throw new RuntimeException('Spojenie A nevytvorilo prvé prijatie.');
            }

            $this->assertStoredCounts($dbA, $requestReference, 1, 1, 2);

            CLI::write('Spojenie B: odoslanie súbežnej rezervácie rovnakej REQUEST_REFERENCE.', 'yellow');
            $asyncInsertStarted = $this->startCompetingInsert($dbB, $payloadFingerprint, $runB);
            if (! $asyncInsertStarted) {
                throw new RuntimeException('Súbežný INSERT spojenia B sa nepodarilo odoslať.');
            }

            usleep(200_000);

            if (! $dbA->transCommit()) {
                throw new RuntimeException('Vonkajšiu transakciu spojenia A sa nepodarilo potvrdiť.');
            }

            $this->expectDuplicateKeyFromAsyncInsert($dbB);
            CLI::write('Kolízia: OK — spojenie B dostalo databázovú kolíziu 1062 po commite spojenia A.', 'green');

            $resultB = FirstAcceptanceServiceFactory::fromConnection($dbB)->accept($payloadFingerprint, $runB);
            if ($resultB->outcome !== ReservationResult::ALREADY_EXISTS) {
                throw new RuntimeException('Opakované prijatie spojenia B nevrátilo ALREADY_EXISTS.');
            }

            if ($resultB->reservation->derivationReference !== $runA->derivationReference) {
                throw new RuntimeException('Spojenie B sa nepriradilo k derivation_reference víťazného toku A.');
            }

            $this->assertStoredCounts($dbB, $requestReference, 1, 1, 2);

            CLI::newLine();
            CLI::write('Súbežné overenie úspešné: vznikla jedna rezervácia, jeden beh a dve doménové väzby; druhý tok sa priradil k existujúcemu behu.', 'green');

            return EXIT_SUCCESS;
        } catch (Throwable $exception) {
            if ($dbA instanceof BaseConnection) {
                $dbA->transRollback();
                $dbA->resetTransStatus();
            }
            if ($dbB instanceof BaseConnection) {
                $dbB->transRollback();
                $dbB->resetTransStatus();
            }

            CLI::error('Súbežné overenie zlyhalo: ' . $exception->getMessage());

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
            throw new RuntimeException('Overenie vyžaduje dve MySQLi spojenia.');
        }

        $dbA->initialize();
        $dbB->initialize();

        if (! $dbA->mysqli instanceof mysqli || ! $dbB->mysqli instanceof mysqli) {
            throw new RuntimeException('MySQLi spojenia sa nepodarilo inicializovať.');
        }

        if ($dbA->mysqli === $dbB->mysqli || $dbA->mysqli->thread_id === $dbB->mysqli->thread_id) {
            throw new RuntimeException('Databázové spojenia A a B nie sú nezávislé.');
        }
    }

    private function startCompetingInsert(Connection $db, string $payloadFingerprint, InitialDerivationRun $run): bool
    {
        $mysqli = $db->mysqli;
        if (! $mysqli instanceof mysqli) {
            throw new RuntimeException('Spojenie B nemá inicializovaný MySQLi objekt.');
        }

        $timestamp = $run->startedAt->format('Y-m-d H:i:s.u');
        $values = array_map(
            static fn (string $value): string => "'" . $mysqli->real_escape_string($value) . "'",
            [
                $run->requestReference,
                $payloadFingerprint,
                $run->derivationReference,
                'RESERVED',
                $timestamp,
                $timestamp,
            ],
        );

        $sql = sprintf(
            'INSERT INTO question_derivation_request_reservations '
            . '(request_reference, payload_fingerprint, derivation_reference, reservation_state, reserved_at, updated_at) '
            . 'VALUES (%s)',
            implode(', ', $values),
        );

        return $mysqli->query($sql, MYSQLI_ASYNC) === true;
    }

    private function expectDuplicateKeyFromAsyncInsert(Connection $db): void
    {
        $mysqli = $db->mysqli;
        if (! $mysqli instanceof mysqli) {
            throw new RuntimeException('Spojenie B nemá inicializovaný MySQLi objekt.');
        }

        $read = [$mysqli];
        $error = [];
        $reject = [];
        $ready = mysqli_poll($read, $error, $reject, 10, 0);
        if ($ready !== 1) {
            throw new RuntimeException('Súbežný INSERT spojenia B sa neukončil v časovom limite.');
        }

        try {
            $mysqli->reap_async_query();
        } catch (mysqli_sql_exception $exception) {
            if ((int) $exception->getCode() === 1062) {
                return;
            }

            throw $exception;
        }

        throw new RuntimeException('Spojenie B neočakávane vytvorilo druhú rezerváciu.');
    }

    private function makeRun(string $requestReference, string $suffix): InitialDerivationRun
    {
        return new InitialDerivationRun(
            derivationReference: 'concurrent-derivation-' . $suffix,
            requestReference: $requestReference,
            responseTargetReference: 'concurrent-response',
            requestSourceSnapshot: 'concurrent-source-snapshot',
            sourceQuestionReference: 'concurrent-question',
            derivationSubjectReference: 'concurrent-subject',
            purposeSnapshot: 'concurrent-purpose',
            contextSnapshot: 'concurrent-context',
            scopeSnapshot: 'concurrent-scope',
            domainTermReferences: ['concurrent-domain-term-a', 'concurrent-domain-term-b'],
            actorReference: 'concurrent-actor',
            authorityContextSnapshot: 'concurrent-authority-context',
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
        } catch (Throwable $cleanupException) {
            CLI::error('Núdzové čistenie súbežných integračných dát zlyhalo: ' . $cleanupException->getMessage());
        }
    }
}
