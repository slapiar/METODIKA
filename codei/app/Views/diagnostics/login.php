<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>METODIKA DB diagnostika</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f8fafc; color: #0f172a; }
        .panel { max-width: 40rem; background: #ffffff; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input[type="password"] { width: 100%; padding: 0.6rem; margin-top: 0.4rem; }
        button { margin-top: 1rem; padding: 0.6rem 1rem; font-weight: 600; }
        .notice { margin-top: 1rem; color: #7f1d1d; font-weight: 600; }
    </style>
</head>
<body>
    <div class="panel">
        <h1>Diagnostika databázy METODIKA</h1>
        <p>Prístup je povolený iba po serverovom overení diagnostického tokenu.</p>
        <form method="post" action="/diagnostics/database/login" autocomplete="off">
            <?= csrf_field() ?>
            <label for="diagnostics_token">Diagnostický token</label>
            <input id="diagnostics_token" name="diagnostics_token" type="password" required>
            <button type="submit">Overiť token</button>
        </form>
        <p class="notice">Diagnostika je iba čítacia. Migrácie neboli spustené.</p>
    </div>
</body>
</html>
