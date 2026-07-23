<?php

declare(strict_types=1);

namespace App\Application\QuestionDerivation\Contracts;

interface TransactionBoundaryPort
{
    public function run(callable $operation): mixed;
}
