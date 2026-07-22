<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

final class VerifyDatabaseCapabilities extends BaseCommand
{
    protected $group = 'METODIKA';
    protected $name = 'metodika:db-capabilities';
    protected $description = 'Overí verziu servera, InnoDB, utf8mb4_bin a DATETIME(6) bez výpisu tajomstiev.';
    protected $usage = 'metodika:db-capabilities';

    public function run(array $params): void
    {
        try {
            $db = db_connect('default');
            $db->initialize();

            $version = $db->query('SELECT VERSION() AS server_version')->getRowArray();
            $engine = $db->query("SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'")->getRowArray();
            $collation = $db->query("SHOW COLLATION LIKE 'utf8mb4_bin'")->getRowArray();
            $datetime = $db->query("SELECT CAST('2026-01-01 00:00:00.123456' AS DATETIME(6)) AS datetime_6")->getRowArray();

            $checks = [
                'Spojenie' => true,
                'Server' => is_string($version['server_version'] ?? null) && $version['server_version'] !== '',
                'InnoDB' => in_array(strtoupper((string) ($engine['SUPPORT'] ?? '')), ['YES', 'DEFAULT'], true),
                'utf8mb4_bin' => $collation !== [],
                'DATETIME(6)' => str_ends_with((string) ($datetime['datetime_6'] ?? ''), '.123456'),
            ];

            CLI::write('Databázový server: ' . (string) ($version['server_version'] ?? 'nezistený'));
            foreach ($checks as $label => $passed) {
                CLI::write(sprintf('%-14s %s', $label . ':', $passed ? 'OK' : 'NIE'), $passed ? 'green' : 'red');
            }

            if (in_array(false, $checks, true)) {
                CLI::error('Databázový server nespĺňa všetky požadované schopnosti.');
                return;
            }

            CLI::write('Všetky požadované schopnosti boli potvrdené.', 'green');
        } catch (Throwable $exception) {
            CLI::error('Databázové overenie zlyhalo: ' . $exception->getMessage());
        }
    }
}
