<?php

declare(strict_types=1);

use App\Services\DiagnosticsProductionStateInspector;
use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class DiagnosticsProductionStateInspectorTest extends CIUnitTestCase
{
    private string $tempRoot = '';

    protected function tearDown(): void
    {
        putenv('METODIKA_DIAGNOSTICS_ENABLED');
        putenv('METODIKA_CONCURRENCY_WEB_ENABLED');

        if ($this->tempRoot !== '') {
            $this->deleteTree($this->tempRoot);
        }

        parent::tearDown();
    }

    public function testInspectReturnsReadOnlyDeploymentDatabaseAndRunStoreState(): void
    {
        $this->tempRoot = sys_get_temp_dir() . '/metodika-production-state-' . bin2hex(random_bytes(4));
        $rootPath = $this->tempRoot . '/root';
        $writePath = $this->tempRoot . '/write';
        $appPath = $this->tempRoot . '/app';

        mkdir($rootPath . '/deploy', 0777, true);
        mkdir($writePath . '/diagnostics/concurrency', 0777, true);
        mkdir($appPath . '/Database/Migrations', 0777, true);

        file_put_contents($rootPath . '/RELEASE_VERSION', "1.2.3\n");
        file_put_contents($rootPath . '/deploy/SOURCE_COMMIT.txt', "0123456789abcdef0123456789abcdef01234567\n");
        file_put_contents($writePath . '/diagnostics/concurrency/run-alpha.json', '{}');
        file_put_contents($writePath . '/diagnostics/concurrency/run-alpha.lock', '');
        file_put_contents($writePath . '/diagnostics/concurrency/run-beta.json.tmp.abcdef', '{}');
        file_put_contents($appPath . '/Database/Migrations/2026-07-22-140001_First.php', '<?php');
        file_put_contents($appPath . '/Database/Migrations/2026-07-22-140002_Second.php', '<?php');

        putenv('METODIKA_DIAGNOSTICS_ENABLED=1');
        putenv('METODIKA_CONCURRENCY_WEB_ENABLED=0');

        $database = new DiagnosticsProductionStateFakeDatabase([
            'migrations' => [
                [
                    'id' => 1,
                    'version' => '2026-07-22-140001',
                    'class' => 'App\\Database\\Migrations\\First',
                    'group' => 'default',
                    'namespace' => 'App',
                    'time' => 1784700000,
                    'batch' => 1,
                ],
            ],
            'ini_sessions' => [['id' => 1], ['id' => 3]],
            'ini_steps' => [['id' => 2]],
            'ini_evidence' => [],
            'question_derivation_request_reservations' => [['id' => 10]],
            'question_derivation_runs' => [['id' => 11]],
            'question_derivation_run_domain_terms' => [['id' => 12], ['id' => 13]],
        ]);

        $inspector = new DiagnosticsProductionStateInspector(
            static fn () => $database,
            $rootPath,
            $writePath,
            $appPath,
        );

        $state = $inspector->inspect();

        $this->assertSame('1.2.3', $state['deployment']['releaseVersion']);
        $this->assertSame('0123456789abcdef0123456789abcdef01234567', $state['deployment']['sourceCommit']);
        $this->assertSame('ANO', $state['flags']['diagnosticsEnabled']);
        $this->assertSame('NIE', $state['flags']['concurrencyWebEnabled']);
        $this->assertTrue($state['database']['connection']);
        $this->assertSame(2, $state['database']['migrations']['availableCount']);
        $this->assertSame(1, $state['database']['migrations']['appliedCount']);
        $this->assertSame(1, $state['database']['migrations']['pendingCount']);
        $this->assertSame('2026-07-22-140002', $state['database']['migrations']['pending'][0]['version']);
        $this->assertSame(2, $state['database']['tables']['ini_sessions']['count']);
        $this->assertSame(3, $state['database']['tables']['ini_sessions']['maxId']);
        $this->assertSame(0, $state['database']['tables']['ini_evidence']['count']);
        $this->assertSame(1, $state['runStore']['jsonCount']);
        $this->assertSame(1, $state['runStore']['lockCount']);
        $this->assertSame(1, $state['runStore']['tempCount']);
        $this->assertSame(0, $state['runStore']['otherCount']);
    }

    public function testInspectUsesGenericErrorCodeWhenDatabaseConnectionFails(): void
    {
        $this->tempRoot = sys_get_temp_dir() . '/metodika-production-state-' . bin2hex(random_bytes(4));
        mkdir($this->tempRoot . '/root', 0777, true);
        mkdir($this->tempRoot . '/write', 0777, true);
        mkdir($this->tempRoot . '/app', 0777, true);

        $inspector = new DiagnosticsProductionStateInspector(
            static fn () => throw new \RuntimeException('secret database detail'),
            $this->tempRoot . '/root',
            $this->tempRoot . '/write',
            $this->tempRoot . '/app',
        );

        $state = $inspector->inspect();

        $this->assertFalse($state['database']['connection']);
        $this->assertSame('DB_CONNECTION_FAILED', $state['database']['errorCode']);
        $this->assertStringNotContainsString('secret database detail', json_encode($state, JSON_THROW_ON_ERROR));
    }

    private function deleteTree(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $entries = scandir($path);
        if (! is_array($entries)) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $child = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($child)) {
                $this->deleteTree($child);
            } else {
                @unlink($child);
            }
        }

        @rmdir($path);
    }
}

final class DiagnosticsProductionStateFakeDatabase
{
    /** @param array<string, list<array<string, mixed>>> $tables */
    public function __construct(private array $tables)
    {
    }

    public function initialize(): void
    {
    }

    public function tableExists(string $table): bool
    {
        return array_key_exists($table, $this->tables);
    }

    public function table(string $table): DiagnosticsProductionStateFakeBuilder
    {
        return new DiagnosticsProductionStateFakeBuilder($this->tables[$table] ?? []);
    }
}

final class DiagnosticsProductionStateFakeBuilder
{
    private bool $selectMax = false;

    /** @param list<array<string, mixed>> $rows */
    public function __construct(private array $rows)
    {
    }

    public function select(string $fields): self
    {
        return $this;
    }

    public function orderBy(string $field, string $direction): self
    {
        if ($field === 'time') {
            usort($this->rows, static fn (array $left, array $right): int => ((int) ($right['time'] ?? 0)) <=> ((int) ($left['time'] ?? 0)));
        }

        return $this;
    }

    public function countAllResults(): int
    {
        return count($this->rows);
    }

    public function selectMax(string $field, string $alias): self
    {
        $this->selectMax = true;
        return $this;
    }

    public function get(): DiagnosticsProductionStateFakeResult
    {
        if ($this->selectMax) {
            $ids = array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $this->rows);
            return new DiagnosticsProductionStateFakeResult([], ['max_id' => $ids === [] ? null : max($ids)]);
        }

        return new DiagnosticsProductionStateFakeResult($this->rows, $this->rows[0] ?? null);
    }
}

final class DiagnosticsProductionStateFakeResult
{
    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed>|null $row
     */
    public function __construct(private array $rows, private ?array $row)
    {
    }

    /** @return list<array<string, mixed>> */
    public function getResultArray(): array
    {
        return $this->rows;
    }

    /** @return array<string, mixed>|null */
    public function getRowArray(): ?array
    {
        return $this->row;
    }
}
