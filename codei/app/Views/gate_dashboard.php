<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>INI Gatekeeper – Sessions</title>
    <link rel="stylesheet" href="<?= esc(asset_url('assets/css/gate-dashboard.css')) ?>">
    <script defer src="<?= esc(asset_url('js/gate-dashboard.js')) ?>"></script>
</head>
<body>
<main>
    <h1>INI Gatekeeper – Sessions</h1>
    <p id="gate-dashboard-status" role="status" aria-live="polite">Načítavam autorizované diagnostické dáta…</p>

    <table
        id="sessions-table"
        data-sessions-url="<?= esc($sessionsUrl) ?>"
        data-session-base-url="<?= esc($sessionBaseUrl) ?>"
    >
        <thead>
            <tr>
                <th>ID</th>
                <th>Projekt</th>
                <th>Agent</th>
                <th>Stav brány</th>
                <th>Vytvorené</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</main>
</body>
</html>
