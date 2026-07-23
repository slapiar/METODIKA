<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fb;
            color: #1e2a3a;
        }

        .wrap {
            max-width: 40rem;
            margin: 12vh auto 0;
            background: #ffffff;
            border: 1px solid #d8e0ec;
            border-radius: 0.7rem;
            padding: 1.4rem;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 2.2rem;
        }

        p {
            margin-top: 0.8rem;
            color: #4b5c75;
        }

        a {
            color: #0b5cab;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover,
        a:focus {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="wrap">
        <h1>404</h1>
        <p>Pozadovany zdroj nebol najdeny.</p>
        <p><a href="<?= esc(site_url('diagnostics/database/login')) ?>">Prihlasit sa do diagnostiky</a></p>
    </main>
</body>
</html>
