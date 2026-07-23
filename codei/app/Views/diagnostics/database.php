<?php
$title = 'METODIKA | DB capability';
$activeNav = 'diagnostics';
$showFooter = false;
?>

<?= $this->extend('layouts/app') ?>

<?= $this->section('meta') ?>
<meta name="robots" content="noindex,nofollow,noarchive">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="panel">
    <h1>Diagnostika databazy METODIKA</h1>
    <table class="table">
        <tr>
            <td>Externe prostredie nacitane</td>
            <td class="<?= $externalEnvironmentLoaded ? 'ok' : 'bad' ?>"><?= $externalEnvironmentLoaded ? 'ANO' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>Databazove spojenie</td>
            <td class="<?= $inspection['connection'] ? 'ok' : 'bad' ?>"><?= $inspection['connection'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>Databazovy server</td>
            <td><?= esc($inspection['serverVersion'] !== '' ? $inspection['serverVersion'] : 'NEZISTENA') ?></td>
        </tr>
        <tr>
            <td>InnoDB</td>
            <td class="<?= $inspection['innodb'] ? 'ok' : 'bad' ?>"><?= $inspection['innodb'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>utf8mb4_bin</td>
            <td class="<?= $inspection['utf8mb4Bin'] ? 'ok' : 'bad' ?>"><?= $inspection['utf8mb4Bin'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>DATETIME(6)</td>
            <td class="<?= $inspection['datetime6'] ? 'ok' : 'bad' ?>"><?= $inspection['datetime6'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>Celkovy vysledok</td>
            <td class="<?= $overallReady ? 'ok' : 'bad' ?>"><?= $overallReady ? 'PRIPRAVENE' : 'NEPRIPRAVENE' ?></td>
        </tr>
        <tr>
            <td>Cas diagnostiky</td>
            <td><?= esc((string) $inspection['diagnosedAt']) ?></td>
        </tr>
    </table>

    <div class="actions">
        <form method="post" action="<?= site_url('diagnostics/database/run') ?>">
            <?= csrf_field() ?>
            <button type="submit">Spustit znovu</button>
        </form>
        <form method="post" action="<?= site_url('diagnostics/database/logout') ?>">
            <?= csrf_field() ?>
            <button type="submit">Odhlasit diagnostiku</button>
        </form>
    </div>

    <p class="notice">Diagnostika je iba citacia. Migracie neboli spustene.</p>
</section>
<?= $this->endSection() ?>
