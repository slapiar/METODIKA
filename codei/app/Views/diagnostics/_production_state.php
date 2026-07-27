<?php

declare(strict_types=1);

use App\Services\DiagnosticsProductionStateInspector;

try {
    $productionState = (new DiagnosticsProductionStateInspector())->inspect();
} catch (\Throwable) {
    $productionState = null;
}

$displayValue = static function (mixed $value): string {
    if ($value === null || $value === '') {
        return 'NEZISTENÉ';
    }

    if (is_bool($value)) {
        return $value ? 'ÁNO' : 'NIE';
    }

    return (string) $value;
};

$statusClass = static function (mixed $value): string {
    if ($value === true || $value === 'ANO' || $value === 0) {
        return 'ok';
    }

    if ($value === false || $value === 'NIE' || $value === 'NEPLATNA_HODNOTA') {
        return 'bad';
    }

    return '';
};
?>
<section class="panel" aria-labelledby="production-state-title">
    <h2 id="production-state-title">Aktuálny produkčný stav — iba čítanie</h2>
    <p class="notice">Výpis neobsahuje diagnostický token, databázové prihlasovacie údaje, DSN ani obsah záznamov.</p>

    <?php if (! is_array($productionState)): ?>
        <p class="bad">Produkčný stav sa nepodarilo bezpečne načítať.</p>
    <?php else: ?>
        <?php
        $deployment = is_array($productionState['deployment'] ?? null) ? $productionState['deployment'] : [];
        $runtime = is_array($productionState['runtime'] ?? null) ? $productionState['runtime'] : [];
        $flags = is_array($productionState['flags'] ?? null) ? $productionState['flags'] : [];
        $database = is_array($productionState['database'] ?? null) ? $productionState['database'] : [];
        $migrations = is_array($database['migrations'] ?? null) ? $database['migrations'] : [];
        $tables = is_array($database['tables'] ?? null) ? $database['tables'] : [];
        $runStore = is_array($productionState['runStore'] ?? null) ? $productionState['runStore'] : [];
        $latestMigration = is_array($migrations['latest'] ?? null) ? $migrations['latest'] : null;
        $pendingMigrations = is_array($migrations['pending'] ?? null) ? $migrations['pending'] : [];
        ?>

        <h3>Nasadenie a runtime</h3>
        <table class="table">
            <tr><td>Nasadená release verzia</td><td><?= esc($displayValue($deployment['releaseVersion'] ?? null)) ?></td></tr>
            <tr><td>Zdrojový commit nasadenia</td><td><code><?= esc($displayValue($deployment['sourceCommit'] ?? null)) ?></code></td></tr>
            <tr><td>PHP</td><td><?= esc($displayValue($runtime['phpVersion'] ?? null)) ?></td></tr>
            <tr><td>PHP SAPI</td><td><?= esc($displayValue($runtime['phpSapi'] ?? null)) ?></td></tr>
            <tr><td>CodeIgniter</td><td><?= esc($displayValue($runtime['codeIgniterVersion'] ?? null)) ?></td></tr>
            <tr><td>CI environment</td><td><?= esc($displayValue($runtime['environment'] ?? null)) ?></td></tr>
            <tr><td>MySQLi rozšírenie</td><td class="<?= $statusClass($runtime['mysqliLoaded'] ?? null) ?>"><?= esc($displayValue($runtime['mysqliLoaded'] ?? null)) ?></td></tr>
            <tr><td>pcntl_fork</td><td class="<?= $statusClass($runtime['pcntlForkAvailable'] ?? null) ?>"><?= esc($displayValue($runtime['pcntlForkAvailable'] ?? null)) ?></td></tr>
            <tr><td>METODIKA_DIAGNOSTICS_ENABLED</td><td class="<?= $statusClass($flags['diagnosticsEnabled'] ?? null) ?>"><?= esc($displayValue($flags['diagnosticsEnabled'] ?? null)) ?></td></tr>
            <tr><td>METODIKA_CONCURRENCY_WEB_ENABLED</td><td class="<?= $statusClass($flags['concurrencyWebEnabled'] ?? null) ?>"><?= esc($displayValue($flags['concurrencyWebEnabled'] ?? null)) ?></td></tr>
            <tr><td>METODIKA_GATE_ENABLED</td><td class="<?= $statusClass($flags['gateEnabled'] ?? null) ?>"><?= esc($displayValue($flags['gateEnabled'] ?? null)) ?></td></tr>
        </table>

        <h3>Stav migrácií</h3>
        <table class="table">
            <tr><td>Tabuľka migrations</td><td class="<?= $statusClass($migrations['tableExists'] ?? null) ?>"><?= esc($displayValue($migrations['tableExists'] ?? null)) ?></td></tr>
            <tr><td>Migračné súbory v nasadení</td><td><?= esc($displayValue($migrations['availableCount'] ?? null)) ?></td></tr>
            <tr><td>Vykonané migrácie</td><td><?= esc($displayValue($migrations['appliedCount'] ?? null)) ?></td></tr>
            <tr><td>Nevykonané migrácie</td><td class="<?= $statusClass($migrations['pendingCount'] ?? null) ?>"><?= esc($displayValue($migrations['pendingCount'] ?? null)) ?></td></tr>
            <tr>
                <td>Posledná vykonaná migrácia</td>
                <td>
                    <?php if ($latestMigration === null): ?>
                        NEZISTENÉ
                    <?php else: ?>
                        <code><?= esc($displayValue($latestMigration['version'] ?? null)) ?></code>
                        — <?= esc($displayValue($latestMigration['class'] ?? null)) ?>,
                        batch <?= esc($displayValue($latestMigration['batch'] ?? null)) ?>,
                        <?= esc($displayValue($latestMigration['time'] ?? null)) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if (($migrations['errorCode'] ?? null) !== null): ?>
                <tr><td>Chyba čítania migrácií</td><td class="bad"><?= esc($displayValue($migrations['errorCode'])) ?></td></tr>
            <?php endif; ?>
        </table>
        <?php if ($pendingMigrations !== []): ?>
            <p><strong>Nevykonané migračné súbory:</strong></p>
            <ul>
                <?php foreach ($pendingMigrations as $migration): ?>
                    <?php if (is_array($migration)): ?>
                        <li><code><?= esc($displayValue($migration['version'] ?? null)) ?></code> — <?= esc($displayValue($migration['class'] ?? null)) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h3>Diagnostické databázové dáta</h3>
        <table class="table">
            <thead><tr><th>Tabuľka</th><th>Existuje</th><th>Počet riadkov</th><th>Najvyššie ID</th></tr></thead>
            <tbody>
            <?php foreach ($tables as $tableState): ?>
                <?php if (is_array($tableState)): ?>
                    <tr>
                        <td><?= esc($displayValue($tableState['label'] ?? null)) ?></td>
                        <td class="<?= $statusClass($tableState['exists'] ?? null) ?>"><?= esc($displayValue($tableState['exists'] ?? null)) ?></td>
                        <td><?= esc($displayValue($tableState['count'] ?? null)) ?></td>
                        <td><?= esc($displayValue($tableState['maxId'] ?? null)) ?></td>
                    </tr>
                    <?php if (($tableState['errorCode'] ?? null) !== null): ?>
                        <tr><td colspan="4" class="bad">Chyba čítania: <?= esc($displayValue($tableState['errorCode'])) ?></td></tr>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Run-store súbory</h3>
        <table class="table">
            <tr><td>Adresár existuje</td><td class="<?= $statusClass($runStore['directoryExists'] ?? null) ?>"><?= esc($displayValue($runStore['directoryExists'] ?? null)) ?></td></tr>
            <tr><td>Adresár je čitateľný</td><td class="<?= $statusClass($runStore['readable'] ?? null) ?>"><?= esc($displayValue($runStore['readable'] ?? null)) ?></td></tr>
            <tr><td>JSON súbory</td><td><?= esc($displayValue($runStore['jsonCount'] ?? null)) ?></td></tr>
            <tr><td>Lock súbory</td><td><?= esc($displayValue($runStore['lockCount'] ?? null)) ?></td></tr>
            <tr><td>Temp súbory</td><td><?= esc($displayValue($runStore['tempCount'] ?? null)) ?></td></tr>
            <tr><td>Iné súbory</td><td><?= esc($displayValue($runStore['otherCount'] ?? null)) ?></td></tr>
            <?php if (($runStore['errorCode'] ?? null) !== null): ?>
                <tr><td>Chyba čítania run-store</td><td class="bad"><?= esc($displayValue($runStore['errorCode'])) ?></td></tr>
            <?php endif; ?>
        </table>
        <p class="notice">Čas read-only výpisu: <?= esc($displayValue($productionState['inspectedAt'] ?? null)) ?></p>
    <?php endif; ?>
</section>
