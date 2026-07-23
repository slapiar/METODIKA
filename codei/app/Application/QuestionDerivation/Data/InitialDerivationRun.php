<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation\Data;

use DateTimeImmutable;
use RuntimeException;

final readonly class InitialDerivationRun
{
    /** @param list<string> $domainTermReferences */
    public function __construct(
        public string $derivationReference,
        public string $requestReference,
        public string $responseTargetReference,
        public string $requestSourceSnapshot,
        public string $sourceQuestionReference,
        public string $derivationSubjectReference,
        public string $purposeSnapshot,
        public string $contextSnapshot,
        public string $scopeSnapshot,
        public array $domainTermReferences,
        public string $actorReference,
        public string $authorityContextSnapshot,
        public string $runMode,
        public DateTimeImmutable $startedAt,
    ) {
        foreach ([
            'derivation_reference' => $this->derivationReference,
            'request_reference' => $this->requestReference,
            'response_target_reference' => $this->responseTargetReference,
            'source_question_reference' => $this->sourceQuestionReference,
            'derivation_subject_reference' => $this->derivationSubjectReference,
            'actor_reference' => $this->actorReference,
        ] as $name => $value) {
            if ($value === '' || mb_strlen($value) > 191) {
                throw new RuntimeException($name . ' musí mať 1 až 191 znakov.');
            }
        }

        if ($this->runMode !== 'PARTIAL_RUN_WITH_ATOMIC_GATE') {
            throw new RuntimeException('run_mode musí byť PARTIAL_RUN_WITH_ATOMIC_GATE.');
        }

        $seen = [];
        foreach ($this->domainTermReferences as $reference) {
            if (! is_string($reference) || $reference === '' || mb_strlen($reference) > 191) {
                throw new RuntimeException('Každá domain_term_reference musí mať 1 až 191 znakov.');
            }

            if (isset($seen[$reference])) {
                throw new RuntimeException('domain_term_references nesmie obsahovať duplicity.');
            }
            $seen[$reference] = true;
        }
    }
}
