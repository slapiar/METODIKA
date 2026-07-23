<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\QuestionDerivation;

use App\Application\QuestionDerivation\FirstAcceptanceService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

final class FirstAcceptanceServiceFactory
{
    public static function fromDefaultConnection(): FirstAcceptanceService
    {
        return self::fromConnection(Database::connect());
    }

    public static function fromConnection(BaseConnection $db): FirstAcceptanceService
    {
        return new FirstAcceptanceService(
            new RequestReferenceRepository($db),
            new DerivationHistoryRepository($db),
            new DatabaseTransactionBoundary($db),
        );
    }
}
