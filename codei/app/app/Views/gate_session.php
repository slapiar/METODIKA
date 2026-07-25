<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>INI Session Detail</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { margin-bottom: 10px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: left; }
        th { background: #f5f5f5; }
        .status-valid { color: #5cb85c; font-weight: bold; }
        .status-invalid { color: #d9534f; font-weight: bold; }
        .status-pending { color: #999; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="/assets/css/gate-session.css">

    <script src="/js/gate-session.js"></script>
    <script src="/js/gate-dashboard.js"></script>

</head>
<body>

<h1>Session #<?= esc($session['id']) ?></h1>

<div class="info">
    <strong>Projekt:</strong> <?= esc($session['project_name']) ?><br>
    <strong>Agent:</strong> <?= esc($session['agent_name']) ?><br>
    <strong>Stav brány:</strong> <?= esc($session['gate_state']) ?><br>
</div>

<h2>Kroky</h2>

<table>
    <thead>
        <tr>
            <th>Krok</th>
            <th>Názov</th>
            <th>Status</th>
            <th>Validované</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($steps as $step): ?>
            <tr>
                <td><?= esc($step['step_number']) ?></td>
                <td><?= esc($step['name']) ?></td>
                <td class="status-<?= esc($step['status']) ?>">
                    <?= esc($step['status']) ?>
                </td>
                <td><?= esc($step['validated_at'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
