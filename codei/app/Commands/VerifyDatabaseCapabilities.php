<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\DatabaseCapabilityInspector;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

final class VerifyDatabaseCapabilities extends BaseCommand
{
    protected $group = 'METODIKA';
    protected $name = 'metodika:db-capabilities';
    protected $description = 'Overí verziu servera, InnoDB, utf8mb4_bin a DATETIME(6) bez výpisu tajomstiev.';
    protected $usage = 'metodika:db-capabilities';

    public function run(array $params)
    {
        /** @var DatabaseCapabilityInspector $inspector */
        $inspector = Services::databaseCapabilityInspector();
        $result = $inspector->inspect();

        CLI::write('Databázový server: ' . ($result['serverVersion'] !== '' ? $result['serverVersion'] : 'nezistený'));
        CLI::write(sprintf('%-14s %s', 'Spojenie:', $result['connection'] ? 'OK' : 'NIE'), $result['connection'] ? 'green' : 'red');
        CLI::write(sprintf('%-14s %s', 'Server:', $result['server'] ? 'OK' : 'NIE'), $result['server'] ? 'green' : 'red');
        CLI::write(sprintf('%-14s %s', 'InnoDB:', $result['innodb'] ? 'OK' : 'NIE'), $result['innodb'] ? 'green' : 'red');
        CLI::write(sprintf('%-14s %s', 'utf8mb4_bin:', $result['utf8mb4Bin'] ? 'OK' : 'NIE'), $result['utf8mb4Bin'] ? 'green' : 'red');
        CLI::write(sprintf('%-14s %s', 'DATETIME(6):', $result['datetime6'] ? 'OK' : 'NIE'), $result['datetime6'] ? 'green' : 'red');

        if ($result['errorCode'] !== null) {
            CLI::error('Databázové overenie zlyhalo. Skontrolujte externú konfiguráciu a dostupnosť servera.');
            return EXIT_ERROR;
        }

        CLI::write('Všetky požadované schopnosti boli potvrdené.', 'green');
        return EXIT_SUCCESS;
    }
}
