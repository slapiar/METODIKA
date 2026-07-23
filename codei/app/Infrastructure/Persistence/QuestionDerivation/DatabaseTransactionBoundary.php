<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\QuestionDerivation;

use App\Application\QuestionDerivation\Contracts\TransactionBoundaryPort;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use RuntimeException;
use Throwable;

final class DatabaseTransactionBoundary implements TransactionBoundaryPort
{
    public function __construct(private readonly BaseConnection $db)
    {
    }

    public static function fromDefaultConnection(): self
    {
        return new self(Database::connect());
    }

    public function run(callable $operation): mixed
    {
        if (! $this->db->transBegin()) {
            throw new RuntimeException('Databázovú transakciu sa nepodarilo začať.');
        }

        try {
            $result = $operation();

            if (! $this->db->transCommit()) {
                throw new RuntimeException('Databázovú transakciu sa nepodarilo potvrdiť.');
            }

            return $result;
        } catch (Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }
}
