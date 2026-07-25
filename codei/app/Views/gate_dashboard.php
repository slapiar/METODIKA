<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>INI Gatekeeper – Sessions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: left; }
        th { background: #f5f5f5; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .state-locked { color: #d9534f; font-weight: bold; }
        .state-verifying { color: #f0ad4e; font-weight: bold; }
        .state-open { color: #5cb85c; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="/assets/css/gate-dashboard.css">

    <script src="/js/gate-dashboard.js"></script>
</head>
<body>
<tbody id="sessions-table"></tbody>

<h1>INI Gatekeeper – Sessions</h1>

<table>
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
    <tbody>
        <?php foreach ($sessions as $s): ?>
            <tr>
                <td><?= esc($s['id']) ?></td>
                <td><?= esc($s['project_name']) ?></td>
                <td><?= esc($s['agent_name']) ?></td>
                <td class="state-<?= esc($s['gate_state']) ?>">
                    <?= esc($s['gate_state']) ?>
                </td>
                <td><?= esc($s['created_at']) ?></td>
                <td><a href="/gate/session/<?= esc($s['id']) ?>">Otvoriť</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
