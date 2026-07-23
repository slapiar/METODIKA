<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

final class VerifyQuestionDerivationSchema extends BaseCommand
{
    protected $group = 'METODIKA';
    protected $name = 'metodika:verify-question-schema';
    protected $description = 'Čítaním INFORMATION_SCHEMA overí tabuľky, engine, collation a cudzie kľúče odvodzovania otázok.';
    protected $usage = 'metodika:verify-question-schema';

    private const EXPECTED_TABLES = [
        'question_derivation_request_reservations',
        'question_derivation_runs',
        'question_derivation_run_domain_terms',
        'question_derivation_branches',
        'question_derivation_branch_dependencies',
        'question_derivation_candidates',
        'question_derivation_run_results',
        'question_derivation_traces',
    ];

    public function run(array $params)
    {
        try {
            $db = Database::connect();
            $databaseName = (string) $db->getDatabase();

            if ($databaseName === '') {
                CLI::error('Nie je určená aktuálna databáza.');
                return EXIT_ERROR;
            }

            $placeholders = implode(', ', array_fill(0, count(self::EXPECTED_TABLES), '?'));
            $tableRows = $db->query(
                "SELECT TABLE_NAME, ENGINE, TABLE_COLLATION
                   FROM INFORMATION_SCHEMA.TABLES
                  WHERE TABLE_SCHEMA = ?
                    AND TABLE_NAME IN ({$placeholders})
                  ORDER BY TABLE_NAME",
                [$databaseName, ...self::EXPECTED_TABLES],
            )->getResultArray();

            $tables = [];
            foreach ($tableRows as $row) {
                $tables[(string) $row['TABLE_NAME']] = $row;
            }

            $ok = true;
            CLI::write('Fyzické tabuľky:', 'yellow');

            foreach (self::EXPECTED_TABLES as $tableName) {
                $row = $tables[$tableName] ?? null;
                $exists = is_array($row);
                $engineOk = $exists && strcasecmp((string) $row['ENGINE'], 'InnoDB') === 0;
                $collationOk = $exists && strcasecmp((string) $row['TABLE_COLLATION'], 'utf8mb4_bin') === 0;
                $tableOk = $exists && $engineOk && $collationOk;
                $ok = $ok && $tableOk;

                CLI::write(sprintf(
                    '%-52s %s  engine=%s  collation=%s',
                    $tableName,
                    $tableOk ? 'OK' : 'NIE',
                    $exists ? (string) $row['ENGINE'] : 'chýba',
                    $exists ? (string) $row['TABLE_COLLATION'] : 'chýba',
                ), $tableOk ? 'green' : 'red');
            }

            $foreignKeys = $db->query(
                "SELECT rc.CONSTRAINT_NAME,
                        rc.TABLE_NAME,
                        kcu.COLUMN_NAME,
                        kcu.REFERENCED_TABLE_NAME,
                        kcu.REFERENCED_COLUMN_NAME,
                        rc.DELETE_RULE,
                        rc.UPDATE_RULE
                   FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc
                   JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
                     ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
                    AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
                    AND kcu.TABLE_NAME = rc.TABLE_NAME
                  WHERE rc.CONSTRAINT_SCHEMA = ?
                    AND rc.TABLE_NAME IN ({$placeholders})
                  ORDER BY rc.TABLE_NAME, rc.CONSTRAINT_NAME, kcu.ORDINAL_POSITION",
                [$databaseName, ...self::EXPECTED_TABLES],
            )->getResultArray();

            CLI::newLine();
            CLI::write('Cudzie kľúče:', 'yellow');

            if ($foreignKeys === []) {
                CLI::error('Nenašiel sa žiadny cudzí kľúč očakávanej schémy.');
                $ok = false;
            }

            foreach ($foreignKeys as $foreignKey) {
                $rulesOk = strcasecmp((string) $foreignKey['DELETE_RULE'], 'RESTRICT') === 0
                    && strcasecmp((string) $foreignKey['UPDATE_RULE'], 'RESTRICT') === 0;
                $ok = $ok && $rulesOk;

                CLI::write(sprintf(
                    '%-28s %s.%s -> %s.%s  DELETE=%s UPDATE=%s  %s',
                    (string) $foreignKey['CONSTRAINT_NAME'],
                    (string) $foreignKey['TABLE_NAME'],
                    (string) $foreignKey['COLUMN_NAME'],
                    (string) $foreignKey['REFERENCED_TABLE_NAME'],
                    (string) $foreignKey['REFERENCED_COLUMN_NAME'],
                    (string) $foreignKey['DELETE_RULE'],
                    (string) $foreignKey['UPDATE_RULE'],
                    $rulesOk ? 'OK' : 'NIE',
                ), $rulesOk ? 'green' : 'red');
            }

            CLI::newLine();
            if (! $ok) {
                CLI::error('Fyzická schéma nezodpovedá všetkým kontrolovaným podmienkam.');
                return EXIT_ERROR;
            }

            CLI::write(sprintf(
                'Overenie úspešné: %d tabuliek a %d riadkov cudzích kľúčov.',
                count($tables),
                count($foreignKeys),
            ), 'green');

            return EXIT_SUCCESS;
        } catch (Throwable $exception) {
            CLI::error('Overenie fyzickej schémy zlyhalo: ' . $exception->getMessage());
            return EXIT_ERROR;
        }
    }
}
