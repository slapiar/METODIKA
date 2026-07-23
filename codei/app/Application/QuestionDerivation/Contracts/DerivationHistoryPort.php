<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation\Contracts;

use App\Application\QuestionDerivation\Data\InitialDerivationRun;

interface DerivationHistoryPort
{
    public function createInitialRun(InitialDerivationRun $run): void;
}
