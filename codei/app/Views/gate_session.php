<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>INI Session #<?= esc((string) $session['id']) ?></title>
    <link rel="stylesheet" href="<?= esc(asset_url('assets/css/gate-session.css')) ?>">
    <script defer src="<?= esc(asset_url('js/gate-session.js')) ?>"></script>
</head>
<body
    data-session-id="<?= esc((string) $session['id']) ?>"
    data-steps-url="<?= esc($stepsUrl) ?>"
    data-state-url="<?= esc($stateUrl) ?>"
    data-step-write-url="<?= esc($stepWriteUrl) ?>"
    data-csrf-name="<?= esc($csrfName) ?>"
    data-csrf-hash="<?= esc($csrfHash) ?>"
>
<main>
    <h1>Session #<?= esc((string) $session['id']) ?></h1>

    <div class="info">
        <strong>Projekt:</strong> <?= esc((string) $session['project_name']) ?><br>
        <strong>Agent:</strong> <?= esc((string) $session['agent_name']) ?><br>
        <strong>Stav brány:</strong>
        <span id="gate-state" class="state-<?= esc((string) $session['gate_state']) ?>">
            <?= esc((string) $session['gate_state']) ?>
        </span>
    </div>

    <p id="gate-session-status" role="status" aria-live="polite">Pripravené.</p>

    <h2>Kroky</h2>
    <table>
        <thead>
            <tr>
                <th>Krok</th>
                <th>Názov</th>
                <th>Status</th>
                <th>Validované</th>
                <th>Akcia</th>
            </tr>
        </thead>
        <tbody id="steps-table">
        <?php foreach ($steps as $step): ?>
            <tr data-step="<?= esc((string) $step['step_number']) ?>">
                <td><?= esc((string) $step['step_number']) ?></td>
                <td><?= esc((string) $step['name']) ?></td>
                <td class="status-<?= esc((string) $step['status']) ?>"><?= esc((string) $step['status']) ?></td>
                <td><?= esc((string) ($step['validated_at'] ?? '-')) ?></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
