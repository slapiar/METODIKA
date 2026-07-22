<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>METODIKA DB capability</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f8fafc; color: #0f172a; }
        .panel { max-width: 50rem; background: #ffffff; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        td { padding: 0.5rem; border-bottom: 1px solid #e2e8f0; }
        td:first-child { font-weight: 600; width: 50%; }
        .ok { color: #166534; font-weight: 700; }
        .bad { color: #991b1b; font-weight: 700; }
        .actions { display: flex; gap: 0.75rem; margin-top: 1rem; }
        button { padding: 0.5rem 0.9rem; font-weight: 600; }
        .notice { margin-top: 1rem; color: #7f1d1d; font-weight: 600; }
    </style>
</head>
<body>
    <div class="panel">
        <h1>Diagnostika databázy METODIKA</h1>
        <table>
            <tr>
                <td>Externé prostredie načítané</td>
                <td class="<?= $externalEnvironmentLoaded ? 'ok' : 'bad' ?>"><?= $externalEnvironmentLoaded ? 'ÁNO' : 'NIE' ?></td>
            </tr>
            <tr>
                <td>Databázové spojenie</td>
                <td class="<?= $inspection['connection'] ? 'ok' : 'bad' ?>"><?= $inspection['connection'] ? 'OK' : 'NIE' ?></td>
            </tr>
            <tr>
                <td>Databázový server</td>
                <td><?= esc($inspection['serverVersion'] !== '' ? $inspection['serverVersion'] : 'NEZISTENÁ') ?></td>
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
                <td>Celkový výsledok</td>
                <td class="<?= $overallReady ? 'ok' : 'bad' ?>"><?= $overallReady ? 'PRIPRAVENÉ' : 'NEPRIPRAVENÉ' ?></td>
            </tr>
            <tr>
                <td>Čas diagnostiky</td>
                <td><?= esc((string) $inspection['diagnosedAt']) ?></td>
            </tr>
        </table>

        <div class="actions">
            <form method="post" action="<?= site_url('diagnostics/database/run') ?>">
                <?= csrf_field() ?>
                <button type="submit">Spustiť znovu</button>
            </form>
            <form method="post" action="<?= site_url('diagnostics/database/logout') ?>">
                <?= csrf_field() ?>
                <button type="submit">Odhlásiť diagnostiku</button>
            </form>
        </div>

        <p class="notice">Diagnostika je iba čítacia. Migrácie neboli spustené.</p>
    </div>
</body>
</html>
