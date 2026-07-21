<?php

declare(strict_types=1);

/**
 * METODIKA setup
 *
 * Vytvorí lokálny konfiguračný súbor ../local-config.php.
 * Súbor local-config.php nie je súčasťou release a je vylúčený cez .gitignore.
 */

const CONFIG_FILE_NAME = 'local-config.php';
const DEFAULT_DB_PORT = 3306;
const DEFAULT_DB_CHARSET = 'utf8mb4';

$configPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . CONFIG_FILE_NAME;
$messages = [];
$errors = [];

function input(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * @return array<string, string>
 */
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
            throw new InvalidArgumentException(
                sprintf('Riadok API kľúčov %d nemá formát NAZOV=hodnota.', $lineNumber + 1)
            );
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2));

        if ($name === '' || !preg_match('/^[A-Za-z][A-Za-z0-9_.-]*$/', $name)) {
            throw new InvalidArgumentException(
                sprintf('Neplatný názov API kľúča na riadku %d.', $lineNumber + 1)
            );
        }

        $result[$name] = $value;
    }

    return $result;
}

/**
 * @param array<string, mixed> $config
 */
function buildConfigPhp(array $config): string
{
    return "<?php\n\n"
        . "declare(strict_types=1);\n\n"
        . "/**\n"
        . " * Lokálna konfigurácia METODIKY.\n"
        . " * Vytvorené pomocou app/setup.php.\n"
        . " * Tento súbor nesmie byť súčasťou release.\n"
        . " */\n\n"
        . 'return ' . var_export($config, true) . ";\n";
}

/**
 * Zapíše súbor cez dočasný súbor a následné premenovanie.
 */
function writeConfigFile(string $path, string $content): void
{
    $directory = dirname($path);

    if (!is_dir($directory)) {
        throw new RuntimeException('Cieľový adresár konfigurácie neexistuje.');
    }

    if (!is_writable($directory)) {
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
    'metodic_password' => '',
    'mapmet_host' => 'localhost',
    'mapmet_port' => (string) DEFAULT_DB_PORT,
    'mapmet_name' => 'u550121827_mapmet',
    'mapmet_user' => '',
    'mapmet_password' => '',
    'db_charset' => DEFAULT_DB_CHARSET,
    'api_keys' => '',
];

$form = [];
foreach ($defaults as $key => $default) {
    $form[$key] = input($key, $default);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $requiredFields = [
            'metodic_host' => 'Host databázy METODIKY',
            'metodic_port' => 'Port databázy METODIKY',
            'metodic_name' => 'Názov databázy METODIKY',
            'metodic_user' => 'Používateľ databázy METODIKY',
            'metodic_password' => 'Heslo databázy METODIKY',
            'mapmet_host' => 'Host databázy MAPMET',
            'mapmet_port' => 'Port databázy MAPMET',
            'mapmet_name' => 'Názov databázy MAPMET',
            'mapmet_user' => 'Používateľ databázy MAPMET',
            'mapmet_password' => 'Heslo databázy MAPMET',
            'db_charset' => 'Kódovanie databáz',
        ];

        foreach ($requiredFields as $key => $label) {
            if ($form[$key] === '') {
                $errors[] = sprintf('Pole „%s“ je povinné.', $label);
            }
        }

        foreach (['metodic_port', 'mapmet_port'] as $portField) {
            $port = filter_var($form[$portField], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 65535],
            ]);

            if ($port === false) {
                $errors[] = sprintf('Pole „%s“ musí obsahovať platný port.', $portField);
            }
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $form['metodic_name'])) {
            $errors[] = 'Názov databázy METODIKY obsahuje nepovolené znaky.';
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $form['mapmet_name'])) {
            $errors[] = 'Názov databázy MAPMET obsahuje nepovolené znaky.';
        }

        $apiKeys = parseKeyValueLines($form['api_keys']);
        $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === '1';

        if (is_file($configPath) && !$overwrite) {
            $errors[] = sprintf(
                'Súbor %s už existuje. Pre jeho nahradenie potvrďte prepísanie.',
                CONFIG_FILE_NAME
            );
        }

        if ($errors === []) {
            $config = [
                'generated_at' => date(DATE_ATOM),
                'databases' => [
                    'metodic' => [
                        'host' => $form['metodic_host'],
                        'port' => (int) $form['metodic_port'],
                        'database' => $form['metodic_name'],
                        'username' => $form['metodic_user'],
                        'password' => $form['metodic_password'],
                        'charset' => $form['db_charset'],
                    ],
                    'mapmet' => [
                        'host' => $form['mapmet_host'],
                        'port' => (int) $form['mapmet_port'],
                        'database' => $form['mapmet_name'],
                        'username' => $form['mapmet_user'],
                        'password' => $form['mapmet_password'],
                        'charset' => $form['db_charset'],
                    ],
                ],
                'api_keys' => $apiKeys,
            ];

            writeConfigFile($configPath, buildConfigPhp($config));
            $messages[] = sprintf('Súbor %s bol úspešne vytvorený.', CONFIG_FILE_NAME);
        }
    } catch (Throwable $exception) {
        $errors[] = $exception->getMessage();
    }
}

$configExists = is_file($configPath);
?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>METODIKA — lokálna konfigurácia</title>
    <style>
        :root { color-scheme: light dark; font-family: system-ui, sans-serif; }
        body { max-width: 920px; margin: 2rem auto; padding: 0 1rem 4rem; line-height: 1.5; }
        fieldset { margin: 1.5rem 0; padding: 1rem; border-radius: .5rem; }
        label { display: block; margin: .75rem 0; font-weight: 600; }
        input, textarea { width: 100%; box-sizing: border-box; padding: .65rem; margin-top: .25rem; }
        textarea { min-height: 8rem; font-family: ui-monospace, monospace; }
        button { padding: .75rem 1.25rem; font-weight: 700; cursor: pointer; }
        .notice { padding: .75rem 1rem; margin: 1rem 0; border: 1px solid currentColor; border-radius: .5rem; }
        .muted { opacity: .75; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0 1rem; }
        code { overflow-wrap: anywhere; }
    </style>
</head>
<body>
    <h1>METODIKA — vytvorenie local-config.php</h1>

    <p>
        Tento formulár vytvorí súbor <code><?= html($configPath) ?></code>.
        Konfiguračný súbor zostáva lokálny a nie je súčasťou release.
    </p>

    <?php if ($configExists): ?>
        <div class="notice">Súbor <code><?= html(CONFIG_FILE_NAME) ?></code> už existuje.</div>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <div class="notice"><?= html($message) ?></div>
    <?php endforeach; ?>

    <?php foreach ($errors as $error): ?>
        <div class="notice"><strong>Chyba:</strong> <?= html($error) ?></div>
    <?php endforeach; ?>

    <form method="post" autocomplete="off">
        <fieldset>
            <legend>Databáza METODIKY</legend>
            <div class="grid">
                <label>Host
                    <input name="metodic_host" value="<?= html($form['metodic_host']) ?>" required>
                </label>
                <label>Port
                    <input name="metodic_port" type="number" min="1" max="65535" value="<?= html($form['metodic_port']) ?>" required>
                </label>
                <label>Názov databázy
                    <input name="metodic_name" value="<?= html($form['metodic_name']) ?>" required>
                </label>
                <label>Používateľ
                    <input name="metodic_user" value="<?= html($form['metodic_user']) ?>" required>
                </label>
            </div>
            <label>Heslo
                <input name="metodic_password" type="password" value="" required>
            </label>
        </fieldset>

        <fieldset>
            <legend>Databáza metodickej mapy MAPMET</legend>
            <div class="grid">
                <label>Host
                    <input name="mapmet_host" value="<?= html($form['mapmet_host']) ?>" required>
                </label>
                <label>Port
                    <input name="mapmet_port" type="number" min="1" max="65535" value="<?= html($form['mapmet_port']) ?>" required>
                </label>
                <label>Názov databázy
                    <input name="mapmet_name" value="<?= html($form['mapmet_name']) ?>" required>
                </label>
                <label>Používateľ
                    <input name="mapmet_user" value="<?= html($form['mapmet_user']) ?>" required>
                </label>
            </div>
            <label>Heslo
                <input name="mapmet_password" type="password" value="" required>
            </label>
        </fieldset>

        <fieldset>
            <legend>Spoločné nastavenia</legend>
            <label>Kódovanie databáz
                <input name="db_charset" value="<?= html($form['db_charset']) ?>" required>
            </label>

            <label>API kľúče a ďalšie lokálne hodnoty
                <textarea name="api_keys" placeholder="NAZOV_KLUCA=hodnota&#10;DALSI_KLUC=hodnota"><?= html($form['api_keys']) ?></textarea>
            </label>
            <p class="muted">Jeden záznam na riadok vo formáte <code>NAZOV=hodnota</code>. Prázdne riadky a riadky začínajúce znakom <code>#</code> sa ignorujú.</p>
        </fieldset>

        <?php if ($configExists): ?>
            <label>
                <input name="overwrite" type="checkbox" value="1" style="width:auto">
                Prepísať existujúci local-config.php
            </label>
        <?php endif; ?>

        <button type="submit">Vytvoriť local-config.php</button>
    </form>
</body>
</html>
