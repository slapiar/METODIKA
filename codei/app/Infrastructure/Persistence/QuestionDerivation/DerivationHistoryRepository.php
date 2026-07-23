<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\QuestionDerivation;

use App\Application\QuestionDerivation\Contracts\DerivationHistoryPort;
use App\Application\QuestionDerivation\Data\InitialDerivationRun;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use RuntimeException;

final class DerivationHistoryRepository implements DerivationHistoryPort
{
    public function __construct(private readonly BaseConnection $db)
    {
    }

    public static function fromDefaultConnection(): self
    {
        return new self(Database::connect());
    }

    public function createInitialRun(InitialDerivationRun $run): void
    {
        $reservation = $this->db->query(
            <<<'SQL'
SELECT id
  FROM question_derivation_request_reservations
 WHERE request_reference = ?
   AND derivation_reference = ?
 LIMIT 1
SQL,
            [$run->requestReference, $run->derivationReference],
        )->getRowArray();

        if (! is_array($reservation)) {
            throw new RuntimeException('Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.');
        }

        $this->db->table('question_derivation_runs')->insert([
            'reservation_id' => (int) $reservation['id'],
            'derivation_reference' => $run->derivationReference,
            'request_reference' => $run->requestReference,
            'response_target_reference' => $run->responseTargetReference,
            'request_source_snapshot' => $run->requestSourceSnapshot,
            'source_question_reference' => $run->sourceQuestionReference,
            'derivation_subject_reference' => $run->derivationSubjectReference,
            'purpose_snapshot' => $run->purposeSnapshot,
            'context_snapshot' => $run->contextSnapshot,
            'scope_snapshot' => $run->scopeSnapshot,
            'actor_reference' => $run->actorReference,
            'authority_context_snapshot' => $run->authorityContextSnapshot,
            'run_mode' => $run->runMode,
            'gate_state' => null,
            'run_state' => null,
            'stop_reason_snapshot' => null,
            'failed_control_reference' => null,
            'started_at' => $run->startedAt->format('Y-m-d H:i:s.u'),
            'completed_at' => null,
        ]);

        $runId = (int) $this->db->insertID();
        if ($runId < 1) {
            throw new RuntimeException('Po založení historického behu nebolo získané platné run_id.');
        }

        foreach ($run->domainTermReferences as $order => $reference) {
            $this->db->table('question_derivation_run_domain_terms')->insert([
                'run_id' => $runId,
                'domain_term_reference' => $reference,
                'canonical_order' => $order,
            ]);
        }
    }
}
