<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use CodeIgniter\CodeIgniter;
use Config\Migrations as MigrationsConfig;
use Throwable;

final class DiagnosticsProductionStateInspector
{
    /** @var array<string, string> */
    private const TRACKED_TABLES = [
        'ini_sessions' => 'INI sessions',
        'ini_steps' => 'INI steps',
        'ini_evidence' => 'INI evidence',
        'question_derivation_request_reservations' => 'Question derivation reservations',
        'question_derivation_runs' => 'Question derivation runs',
        'question_derivation_run_domain_terms' => 'Question derivation run domain terms',
    ];

    private Closure $connector;
    private string $rootPath;
    private string $writePath;
    private string $appPath;

    public function __construct(
        ?Closure $connector = null,
        ?string $rootPath = null,
        ?string $writePath = null,
        ?string $appPath = null,
    ) {
        $this->connector = $connector ?? static fn () => db_connect('default');
        $this->rootPath = rtrim($rootPath ?? ROOTPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->writePath = rtrim($writePath ?? WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->appPath = rtrim($appPath ?? APPPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /** @return array<string, mixed> */
    public function inspect(): array
    {
        return [
            'deployment' => [
                'releaseVersion' => $this->readFirstMarker([
                    $this->rootPath . 'RELEASE_VERSION',
                    dirname($this->rootPath) . DIRECTORY_SEPARATOR . 'RELEASE_VERSION',
                    $this->rootPath . 'deploy' . DIRECTORY_SEPARATOR . 'RELEASE_VERSION.txt',
                ]),
                'sourceCommit' => $this->sourceCommit(),
            ],
            'runtime' => [
                'phpVersion' => PHP_VERSION,
                'phpSapi' => PHP_SAPI,
                'codeIgniterVersion' => CodeIgniter::CI_VERSION,
                'environment' => $this->runtimeEnvironment(),
                'mysqliLoaded' => extension_loaded('mysqli'),
                'pcntlForkAvailable' => function_exists('pcntl_fork'),
            ],
            'flags' => [
                'diagnosticsEnabled' => $this->environmentFlag('METODIKA_DIAGNOSTICS_ENABLED'),
                'concurrencyWebEnabled' => $this->environmentFlag('METODIKA_CONCURRENCY_WEB_ENABLED'),
                'gateEnabled' => $this->environmentFlag('METODIKA_GATE_ENABLED'),
            ],
            'database' => $this->inspectDatabase(),
            'runStore' => $this->inspectRunStore(),
            'inspectedAt' => gmdate('c'),
        ];
    }

    /** @return array<string, mixed> */
    private function inspectDatabase(): array
    {
        $result = [
            'connection' => false,
            'errorCode' => null,
            'migrations' => $this->emptyMigrationState(),
            'tables' => $this->emptyTableStates(),
        ];

        try {
            $db = ($this->connector)();
            $db->initialize();
            $result['connection'] = true;
        } catch (Throwable) {
            $result['errorCode'] = 'DB_CONNECTION_FAILED';
            return $result;
        }

        $result['migrations'] = $this->inspectMigrations($db);

        foreach (self::TRACKED_TABLES as $table => $label) {
            $result['tables'][$table] = $this->inspectTable($db, $table, $label);
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function inspectMigrations(mixed $db): array
    {
        $available = $this->availableMigrations();
        $state = $this->emptyMigrationState();
        $state['availableCount'] = count($available);
        $state['available'] = array_values($available);

        $table = (new MigrationsConfig())->table;

        try {
            $state['tableExists'] = $db->tableExists($table);
        } catch (Throwable) {
            $state['errorCode'] = 'MIGRATIONS_TABLE_CHECK_FAILED';
            return $state;
        }

        if ($state['tableExists'] !== true) {
            $state['pendingCount'] = count($available);
            $state['pending'] = array_values($available);
            return $state;
        }

        try {
            $rows = $db->table($table)
                ->select('version, class, group, namespace, time, batch')
                ->orderBy('time', 'DESC')
                ->orderBy('batch', 'DESC')
                ->get()
                ->getResultArray();
        } catch (Throwable) {
            $state['errorCode'] = 'MIGRATIONS_READ_FAILED';
            return $state;
        }

        $rows = is_array($rows) ? $rows : [];
        $state['appliedCount'] = count($rows);

        if ($rows !== []) {
            $latest = $rows[0];
            $state['latest'] = [
                'version' => isset($latest['version']) ? (string) $latest['version'] : null,
                'class' => isset($latest['class']) ? (string) $latest['class'] : null,
                'batch' => isset($latest['batch']) && is_numeric($latest['batch']) ? (int) $latest['batch'] : null,
                'time' => isset($latest['time']) && is_numeric($latest['time'])
                    ? gmdate('c', (int) $latest['time'])
                    : null,
            ];
        }

        $appliedVersions = [];
        foreach ($rows as $row) {
            if (isset($row['version'])) {
                $appliedVersions[(string) $row['version']] = true;
            }
        }

        $pending = [];
        foreach ($available as $version => $migration) {
            if (! isset($appliedVersions[$version])) {
                $pending[] = $migration;
            }
        }

        $state['pendingCount'] = count($pending);
        $state['pending'] = $pending;

        return $state;
    }

    /** @return array<string, mixed> */
    private function inspectTable(mixed $db, string $table, string $label): array
    {
        $state = [
            'label' => $label,
            'exists' => false,
            'count' => null,
            'maxId' => null,
            'errorCode' => null,
        ];

        try {
            $state['exists'] = $db->tableExists($table);
        } catch (Throwable) {
            $state['errorCode'] = 'TABLE_CHECK_FAILED';
            return $state;
        }

        if ($state['exists'] !== true) {
            return $state;
        }

        try {
            $state['count'] = (int) $db->table($table)->countAllResults();
        } catch (Throwable) {
            $state['errorCode'] = 'TABLE_COUNT_FAILED';
            return $state;
        }

        try {
            $row = $db->table($table)->selectMax('id', 'max_id')->get()->getRowArray();
            if (is_array($row) && isset($row['max_id']) && is_numeric($row['max_id'])) {
                $state['maxId'] = (int) $row['max_id'];
            }
        } catch (Throwable) {
            $state['errorCode'] = 'TABLE_MAX_ID_FAILED';
        }

        return $state;
    }

    /** @return array<string, mixed> */
    private function inspectRunStore(): array
    {
        $directory = $this->writePath . 'diagnostics' . DIRECTORY_SEPARATOR . 'concurrency';
        $state = [
            'directoryExists' => is_dir($directory),
            'readable' => false,
            'jsonCount' => null,
            'lockCount' => null,
            'tempCount' => null,
            'otherCount' => null,
            'errorCode' => null,
        ];

        if ($state['directoryExists'] !== true) {
            $state['readable'] = true;
            $state['jsonCount'] = 0;
            $state['lockCount'] = 0;
            $state['tempCount'] = 0;
            $state['otherCount'] = 0;
            return $state;
        }

        if (! is_readable($directory)) {
            $state['errorCode'] = 'RUN_STORE_NOT_READABLE';
            return $state;
        }

        $entries = @scandir($directory);
        if (! is_array($entries)) {
            $state['errorCode'] = 'RUN_STORE_READ_FAILED';
            return $state;
        }

        $state['readable'] = true;
        $state['jsonCount'] = 0;
        $state['lockCount'] = 0;
        $state['tempCount'] = 0;
        $state['otherCount'] = 0;

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $entry;
            if (! is_file($path)) {
                continue;
            }
            if (str_ends_with($entry, '.json')) {
                $state['jsonCount']++;
            } elseif (str_ends_with($entry, '.lock')) {
                $state['lockCount']++;
            } elseif (str_contains($entry, '.json.tmp.')) {
                $state['tempCount']++;
            } else {
                $state['otherCount']++;
            }
        }

        return $state;
    }

    /** @return array<string, array<string, mixed>> */
    private function emptyTableStates(): array
    {
        $states = [];
        foreach (self::TRACKED_TABLES as $table => $label) {
            $states[$table] = [
                'label' => $label,
                'exists' => false,
                'count' => null,
                'maxId' => null,
                'errorCode' => null,
            ];
        }

        return $states;
    }

    /** @return array<string, mixed> */
    private function emptyMigrationState(): array
    {
        return [
            'tableExists' => false,
            'availableCount' => null,
            'appliedCount' => null,
            'pendingCount' => null,
            'available' => [],
            'pending' => [],
            'latest' => null,
            'errorCode' => null,
        ];
    }

    /** @return array<string, array{version: string, class: string}> */
    private function availableMigrations(): array
    {
        $paths = glob($this->appPath . 'Database' . DIRECTORY_SEPARATOR . 'Migrations' . DIRECTORY_SEPARATOR . '*.php');
        if (! is_array($paths)) {
            return [];
        }

        $migrations = [];
        foreach ($paths as $path) {
            $basename = basename($path);
            if (preg_match('/^(\d{4}-\d{2}-\d{2}-\d{6})_(.+)\.php$/', $basename, $matches) !== 1) {
                continue;
            }

            $migrations[$matches[1]] = [
                'version' => $matches[1],
                'class' => $matches[2],
            ];
        }

        ksort($migrations, SORT_STRING);
        return $migrations;
    }

    private function runtimeEnvironment(): string
    {
        if (defined('ENVIRONMENT')) {
            $environment = constant('ENVIRONMENT');
            if (is_string($environment) && trim($environment) !== '') {
                return trim($environment);
            }
        }

        $environment = getenv('CI_ENVIRONMENT');
        return is_string($environment) && trim($environment) !== '' ? trim($environment) : 'NEZISTENE';
    }

    private function environmentFlag(string $name): string
    {
        $value = getenv($name);
        if (! is_string($value)) {
            return 'NEZISTENE';
        }

        return match (trim($value)) {
            '1' => 'ANO',
            '0' => 'NIE',
            default => 'NEPLATNA_HODNOTA',
        };
    }

    /** @param list<string> $paths */
    private function readFirstMarker(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (! is_file($path) || ! is_readable($path)) {
                continue;
            }

            $value = trim((string) @file_get_contents($path));
            if ($value !== '') {
                return substr($value, 0, 191);
            }
        }

        return null;
    }

    private function sourceCommit(): ?string
    {
        $marker = $this->readFirstMarker([
            $this->rootPath . 'deploy' . DIRECTORY_SEPARATOR . 'SOURCE_COMMIT.txt',
            $this->rootPath . 'SOURCE_COMMIT',
        ]);

        if (is_string($marker) && preg_match('/^[0-9a-f]{7,40}$/i', $marker) === 1) {
            return strtolower($marker);
        }

        $gitDirectory = $this->rootPath . '.git';
        $headPath = $gitDirectory . DIRECTORY_SEPARATOR . 'HEAD';
        if (! is_file($headPath) || ! is_readable($headPath)) {
            return null;
        }

        $head = trim((string) @file_get_contents($headPath));
        if (preg_match('/^[0-9a-f]{40}$/i', $head) === 1) {
            return strtolower($head);
        }

        if (! str_starts_with($head, 'ref: ')) {
            return null;
        }

        $ref = trim(substr($head, 5));
        if ($ref === '' || str_contains($ref, '..')) {
            return null;
        }

        $refPath = $gitDirectory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ref);
        if (! is_file($refPath) || ! is_readable($refPath)) {
            return null;
        }

        $commit = trim((string) @file_get_contents($refPath));
        return preg_match('/^[0-9a-f]{40}$/i', $commit) === 1 ? strtolower($commit) : null;
    }
}
