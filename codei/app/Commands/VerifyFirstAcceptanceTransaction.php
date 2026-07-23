<?php

declare(strict_types=1);

namespace App\Commands;

use App\Application\QuestionDerivation\Contracts\DerivationHistoryPort;
use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use App\Application\QuestionDerivation\FirstAcceptanceService;
use App\Infrastructure\Persistence\QuestionDerivation\DatabaseTransactionBoundary;
use App\Infrastructure\Persistence\QuestionDerivation\DerivationHistoryRepository;
use App\Infrastructure\Persistence\QuestionDerivation\FirstAcceptanceServiceFactory;
use App\Infrastructure\Persistence\QuestionDerivation\RequestReferenceRepository;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class VerifyFirstAcceptanceTransaction extends BaseCommand
{
    protected $group = 'METODIKA';
    protected $name = 'metodika:verify-first-acceptance-transaction';
    protected $description = 'Bez trvalých dát overí commit vetvu a rollback atómového prvého prijatia nad skutočnou databázou.';
    protected $usage = 'metodika:verify-first-acceptance-transaction';

    public function run(array $params)
    {
        $db = null;
        $references = [];

        try {
            $db = Database::connect();
            $token = bin2hex(random_bytes(8));

            $successRun = $this->makeRun('success-' . $token);
            $failureRun = $this->makeRun('failure-' . $token);
            $references = [$successRun->requestReference, $failureRun->requestReference];

            CLI::write('Scenár A: úplný zápis v nadradenej transakcii a následný rollback testovacích dát.', 'yellow');
            $this->verifySuccessfulAtomicWrite($db, $successRun);
            CLI::write('Scenár A: OK — rezervácia, beh a doménové väzby vznikli spolu a po rollbacku nezostali v databáze.', 'green');

            CLI::newLine();
            CLI::write('Scenár B: úmyselná chyba po založení historického behu.', 'yellow');
            $this->verifyRollbackAfterFailure($db, $failureRun);
            CLI::write('Scenár B: OK — chyba vrátila späť rezerváciu, beh aj doménové väzby.', 'green');

            CLI::newLine();
            CLI::write('Integračné overenie úspešné: transakcia prvého prijatia je atómová v oboch kontrolovaných scenároch.', 'green');

            return EXIT_SUCCESS;
        } catch (Throwable $exception) {
            CLI::error('Integračné overenie zlyhalo: ' . $exception->getMessage());

            return EXIT_ERROR;
        } finally {
            if ($db instanceof BaseConnection && $references !== []) {
                $this->emergencyCleanup($db, $references);
            }
        }
    }

    private function verifySuccessfulAtomicWrite(BaseConnection $db, InitialDerivationRun $run): void
    {
        if (! $db->transBegin()) {
            throw new RuntimeException('Nadradenú testovaciu transakciu sa nepodarilo začať.');
        }

        try {
            $result = FirstAcceptanceServiceFactory::fromConnection($db)->accept(
                hash('sha256', $run->requestSourceSnapshot),
                $run,
            );

            if ($result->outcome !== $result::CREATED) {
                throw new RuntimeException('Prvé prijatie nevytvorilo novú rezerváciu.');
            }

            $this->assertStoredCounts($db, $run, 1, 1, count($run->domainTermReferences));
        } finally {
            $db->transRollback();
        }

        $this->assertStoredCounts($db, $run, 0, 0, 0);
    }

    private function verifyRollbackAfterFailure(BaseConnection $db, InitialDerivationRun $run): void
    {
        $realHistory = new DerivationHistoryRepository($db);
        $failingHistory = new class ($realHistory) implements DerivationHistoryPort {
            public function __construct(private readonly DerivationHistoryPort $inner)
            {
            }

            public function createInitialRun(InitialDerivationRun $run): void
            {
                $this->inner->createInitialRun($run);
                throw new RuntimeException('Úmyselná integračná chyba po zápise historického behu.');
            }
        };

        $service = new FirstAcceptanceService(
            new RequestReferenceRepository($db),
            $failingHistory,
            new DatabaseTransactionBoundary($db),
        );

        try {
            $service->accept(hash('sha256', $run->requestSourceSnapshot), $run);
            throw new RuntimeException('Chybový scenár neočakávane neskončil výnimkou.');
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() !== 'Úmyselná integračná chyba po zápise historického behu.') {
                throw $exception;
            }
        }

        $this->assertStoredCounts($db, $run, 0, 0, 0);
    }

    private function makeRun(string $suffix): InitialDerivationRun
    {
        return new InitialDerivationRun(
            derivationReference: 'integration-derivation-' . $suffix,
            requestReference: 'integration-request-' . $suffix,
            responseTargetReference: 'integration-response-' . $suffix,
            requestSourceSnapshot: 'integration-source-snapshot-' . $suffix,
            sourceQuestionReference: 'integration-question-' . $suffix,
            derivationSubjectReference: 'integration-subject-' . $suffix,
            purposeSnapshot: 'integration-purpose',
            contextSnapshot: 'integration-context',
            scopeSnapshot: 'integration-scope',
            domainTermReferences: [
                'integration-domain-term-a-' . $suffix,
                'integration-domain-term-b-' . $suffix,
            ],
            actorReference: 'integration-actor',
            authorityContextSnapshot: 'integration-authority-context',
            runMode: 'PARTIAL_RUN_WITH_ATOMIC_GATE',
            startedAt: new DateTimeImmutable('now'),
        );
    }

    private function assertStoredCounts(
        BaseConnection $db,
        InitialDerivationRun $run,
        int $expectedReservations,
        int $expectedRuns,
        int $expectedDomainTerms,
    ): void {
        $reservationCount = (int) $db->table('question_derivation_request_reservations')
            ->where('request_reference', $run->requestReference)
            ->countAllResults();

        $runRows = $db->table('question_derivation_runs')
            ->select('id')
            ->where('request_reference', $run->requestReference)
            ->where('derivation_reference', $run->derivationReference)
            ->get()
            ->getResultArray();

        $runCount = count($runRows);
        $runIds = array_map(static fn (array $row): int => (int) $row['id'], $runRows);
        $domainTermCount = $runIds === []
            ? 0
            : (int) $db->table('question_derivation_run_domain_terms')
                ->whereIn('run_id', $runIds)
                ->countAllResults();

        if (
            $reservationCount !== $expectedReservations
            || $runCount !== $expectedRuns
            || $domainTermCount !== $expectedDomainTerms
        ) {
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

    /** @param list<string> $requestReferences */
    private function emergencyCleanup(BaseConnection $db, array $requestReferences): void
    {
        try {
            while ($db->transDepth > 0) {
                $db->transRollback();
            }

            $runRows = $db->table('question_derivation_runs')
                ->select('id')
                ->whereIn('request_reference', $requestReferences)
                ->get()
                ->getResultArray();
            $runIds = array_map(static fn (array $row): int => (int) $row['id'], $runRows);

            if ($runIds !== []) {
                $db->table('question_derivation_run_domain_terms')->whereIn('run_id', $runIds)->delete();
                $db->table('question_derivation_runs')->whereIn('id', $runIds)->delete();
            }

            $db->table('question_derivation_request_reservations')
                ->whereIn('request_reference', $requestReferences)
                ->delete();
        } catch (Throwable $cleanupException) {
            CLI::error('Núdzové čistenie integračných dát zlyhalo: ' . $cleanupException->getMessage());
        }
    }
}
