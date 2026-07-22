<?php

declare(strict_types=1);

/**
 * METODIKA setup
 *
 * Webový setup je zámerne uzamknutý. Pred použitím musí byť na serveri
 * nastavená premenná prostredia METODIKA_SETUP_TOKEN.
 *
 * Voliteľné:
 * - METODIKA_SETUP_ALLOW_OVERWRITE=1 dovolí nahradiť existujúcu konfiguráciu.
 * - METODIKA_SETUP_ALLOW_HTTP=1 dovolí spustenie bez HTTPS (iba lokálny vývoj).
 */

const CONFIG_FILE_NAME = 'local-config.php';
const DEFAULT_DB_PORT = 3306;
const DEFAULT_DB_CHARSET = 'utf8mb4';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
header('Referrer-Policy: no-referrer');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'cookie_secure' => isHttps(),
        'use_strict_mode' => true,
    ]);
}

$configPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . CONFIG_FILE_NAME;
$messages = [];
$errors = [];
$setupToken = getenv('METODIKA_SETUP_TOKEN');
$allowOverwrite = getenv('METODIKA_SETUP_ALLOW_OVERWRITE') === '1';
$allowHttp = getenv('METODIKA_SETUP_ALLOW_HTTP') === '1';

if (!is_string($setupToken) || $setupToken === '') {
    http_response_code(503);
    exit('METODIKA setup je uzamknutý. Na serveri chýba METODIKA_SETUP_TOKEN.');
}

if (!isHttps() && !$allowHttp) {
    http_response_code(403);
    exit('METODIKA setup vyžaduje HTTPS. Pre lokálny vývoj možno výslovne povoliť METODIKA_SETUP_ALLOW_HTTP=1.');
}

if (!isset($_SESSION['setup_csrf']) || !is_string($_SESSION['setup_csrf'])) {
    $_SESSION['setup_csrf'] = bin2hex(random_bytes(32));
}

function isHttps(): bool
{
    if (($_SERVER['HTTPS'] ?? '') === 'on') {
        return true;
    }

    return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function input(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** @return array<string, string> */
function parseKeyValueLines(string $source): array
{
    $result = [];
    $lines = preg_split('/\R/u', $source) ?: [];

    foreach ($lines as $lineNumber => $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            throw new InvalidArgumentException(sprintf(
                'Riadok API kľúčov %d nemá formát NAZOV=hodnota.',
                $lineNumber + 1
            ));
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2));
        if ($name === '' || !preg_match('/^[A-Za-z][A-Za-z0-9_.-]*$/', $name)) {
            throw new InvalidArgumentException(sprintf(
                'Neplatný názov API kľúča na riadku %d.',
                $lineNumber + 1
            ));
        }

        $result[$name] = $value;
    }

    return $result;
}

/** @param array<string, mixed> $config */
function buildConfigPhp(array $config): string
{
    return "<?php\n\ndeclare(strict_types=1);\n\n"
        . "/** Lokálna konfigurácia METODIKY. Tento súbor nesmie byť súčasťou release. */\n\n"
        . 'return ' . var_export($config, true) . ";\n";
}

function writeConfigFile(string $path, string $content): void
{
    $directory = dirname($path);
    if (!is_dir($directory) || !is_writable($directory)) {
        throw new RuntimeException('Cieľový adresár konfigurácie nie je zapisovateľný.');
    }

    $temporaryPath = tempnam($directory, '.local-config-');
    if ($temporaryPath === false) {
        throw new RuntimeException('Nepodarilo sa vytvoriť dočasný konfiguračný súbor.');
    }

    try {
        if (file_put_contents($temporaryPath, $content, LOCK_EX) === false) {
            throw new RuntimeException('Nepodarilo sa zapísať dočasný konfiguračný súbor.');
        }
        @chmod($temporaryPath, 0600);
        if (!rename($temporaryPath, $path)) {
            throw new RuntimeException('Nepodarilo sa presunúť konfiguráciu na cieľové miesto.');
        }
    } finally {
        if (is_file($temporaryPath)) {
            @unlink($temporaryPath);
        }
    }
}

$defaults = [
    'metodic_host' => 'localhost',
    'metodic_port' => (string) DEFAULT_DB_PORT,
    'metodic_name' => 'u550121827_metodic',
    'metodic_user' => 'u550121827_metodic',
    'mapmet_host' => 'localhost',
    'mapmet_port' => (string) DEFAULT_DB_PORT,
    'mapmet_name' => 'u550121827_mapmet',
    'mapmet_user' => '',
    'db_charset' => DEFAULT_DB_CHARSET,
    'api_keys' => '',
];

$form = [];
foreach ($defaults as $key => $default) {
    $form[$key] = input($key, $default);
}

$configExists = is_file($configPath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $submittedCsrf = input('csrf_token');
        $submittedToken = input('setup_token');

        if (!hash_equals((string) $_SESSION['setup_csrf'], $submittedCsrf)) {
            throw new RuntimeException('Neplatný bezpečnostný token formulára. Obnovte stránku.');
        }

        if (!hash_equals($setupToken, $submittedToken)) {
            throw new RuntimeException('Neplatný prístupový token setupu.');
        }

        if ($configExists && !$allowOverwrite) {
            throw new RuntimeException(
                'Konfigurácia už existuje a setup je uzamknutý. Pre vedomé prepísanie nastavte METODIKA_SETUP_ALLOW_OVERWRITE=1.'
            );
        }

        $requiredFields = [
            'metodic_host', 'metodic_port', 'metodic_name', 'metodic_user', 'metodic_password',
            'mapmet_host', 'mapmet_port', 'mapmet_name', 'mapmet_user', 'mapmet_password', 'db_charset',
        ];

        foreach ($requiredFields as $key) {
            if (input($key) === '') {
                $errors[] = sprintf('Pole „%s“ je povinné.', $key);
            }
        }

        foreach (['metodic_port', 'mapmet_port'] as $portField) {
            if (filter_var(input($portField), FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 65535],
            ]) === false) {
                $errors[] = sprintf('Pole „%s“ musí obsahovať platný port.', $portField);
            }
        }

        foreach (['metodic_name', 'mapmet_name'] as $databaseField) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', input($databaseField))) {
                $errors[] = sprintf('Pole „%s“ obsahuje nepovolené znaky.', $databaseField);
            }
        }

        $apiKeys = parseKeyValueLines(input('api_keys'));

        if ($errors === []) {
            $config = [
                'generated_at' => date(DATE_ATOM),
                'databases' => [
                    'metodic' => [
                        'host' => input('metodic_host'),
                        'port' => (int) input('metodic_port'),
                        'database' => input('metodic_name'),
                        'username' => input('metodic_user'),
                        'password' => input('metodic_password'),
                        'charset' => input('db_charset'),
                    ],
                    'mapmet' => [
                        'host' => input('mapmet_host'),
                        'port' => (int) input('mapmet_port'),
                        'database' => input('mapmet_name'),
                        'username' => input('mapmet_user'),
                        'password' => input('mapmet_password'),
                        'charset' => input('db_charset'),
                    ],
                ],
                'api_keys' => $apiKeys,
            ];

            writeConfigFile($configPath, buildConfigPhp($config));
            session_regenerate_id(true);
            $_SESSION['setup_csrf'] = bin2hex(random_bytes(32));
            $messages[] = sprintf('Súbor %s bol bezpečne vytvorený.', CONFIG_FILE_NAME);
            $configExists = true;
        }
    } catch (Throwable $exception) {
        $errors[] = $exception->getMessage();
    }
}
?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>METODIKA — zabezpečený setup</title>
    <style>
        :root { color-scheme: light dark; font-family: system-ui, sans-serif; }
        body { max-width: 920px; margin: 2rem auto; padding: 0 1rem 4rem; line-height: 1.5; }
        fieldset { margin: 1.5rem 0; padding: 1rem; border-radius: .5rem; }
        label { display: block; margin: .75rem 0; font-weight: 600; }
        input, textarea { width: 100%; box-sizing: border-box; padding: .65rem; margin-top: .25rem; }
        textarea { min-height: 8rem; font-family: ui-monospace, monospace; }
        button { padding: .75rem 1.25rem; font-weight: 700; cursor: pointer; }
        .notice { padding: .75rem 1rem; margin: 1rem 0; border: 1px solid currentColor; border-radius: .5rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0 1rem; }
    </style>
</head>
<body>
<h1>METODIKA — zabezpečený setup</h1>
<p>Setup funguje iba s prístupovým tokenom uloženým na serveri a zapisuje konfiguráciu s právami <code>0600</code>.</p>

<?php if ($configExists && !$allowOverwrite): ?>
    <div class="notice"><strong>Uzamknuté:</strong> konfigurácia už existuje. Formulár ju bez výslovného serverového povolenia neprepíše.</div>
<?php endif; ?>
<?php foreach ($messages as $message): ?><div class="notice"><?= html($message) ?></div><?php endforeach; ?>
<?php foreach ($errors as $error): ?><div class="notice"><strong>Chyba:</strong> <?= html($error) ?></div><?php endforeach; ?>

<form method="post" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= html((string) $_SESSION['setup_csrf']) ?>">
    <fieldset>
        <legend>Prístup</legend>
        <label>Setup token<input name="setup_token" type="password" required autocomplete="new-password"></label>
    </fieldset>
    <fieldset>
        <legend>Databáza METODIKY</legend>
        <div class="grid">
            <label>Host<input name="metodic_host" value="<?= html($form['metodic_host']) ?>" required></label>
            <label>Port<input name="metodic_port" type="number" min="1" max="65535" value="<?= html($form['metodic_port']) ?>" required></label>
            <label>Názov databázy<input name="metodic_name" value="<?= html($form['metodic_name']) ?>" required></label>
            <label>Používateľ<input name="metodic_user" value="<?= html($form['metodic_user']) ?>" required></label>
        </div>
        <label>Heslo<input name="metodic_password" type="password" required autocomplete="new-password"></label>
    </fieldset>
    <fieldset>
        <legend>Databáza MAPMET</legend>
        <div class="grid">
            <label>Host<input name="mapmet_host" value="<?= html($form['mapmet_host']) ?>" required></label>
            <label>Port<input name="mapmet_port" type="number" min="1" max="65535" value="<?= html($form['mapmet_port']) ?>" required></label>
            <label>Názov databázy<input name="mapmet_name" value="<?= html($form['mapmet_name']) ?>" required></label>
            <label>Používateľ<input name="mapmet_user" value="<?= html($form['mapmet_user']) ?>" required></label>
        </div>
        <label>Heslo<input name="mapmet_password" type="password" required autocomplete="new-password"></label>
    </fieldset>
    <fieldset>
        <legend>Spoločné nastavenia</legend>
        <label>Kódovanie databáz<input name="db_charset" value="<?= html($form['db_charset']) ?>" required></label>
        <label>API kľúče a lokálne hodnoty<textarea name="api_keys" placeholder="NAZOV_KLUCA=hodnota"><?= html($form['api_keys']) ?></textarea></label>
    </fieldset>
    <button type="submit"<?= $configExists && !$allowOverwrite ? ' disabled' : '' ?>>Vytvoriť konfiguráciu</button>
</form>
</body>
</html>
