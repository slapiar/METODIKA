<?php

declare(strict_types=1);

namespace Config;

use RuntimeException;

final class ExternalEnvironment
{
    private static bool $loaded = false;

    public static function load(): void
    {
        $configuredPath = getenv('METODIKA_ENV_FILE');
        $path = null;

        if (is_string($configuredPath) && $configuredPath !== '') {
            $path = $configuredPath;
        } else {
            $candidatePaths = [
                dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . '.env',
                dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'metodika.env',
                dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . '.env',
                dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'metodika.env',
            ];

            foreach ($candidatePaths as $candidatePath) {
                if (is_file($candidatePath)) {
                    $path = $candidatePath;
                    break;
                }
            }
        }

        if (!is_string($path) || $path === '' || !is_file($path)) {
            self::$loaded = false;
            return;
        }

        if (!is_readable($path)) {
            throw new RuntimeException('Externý súbor prostredia METODIKY nie je čitateľný.');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new RuntimeException('Externý súbor prostredia METODIKY sa nepodarilo načítať.');
        }

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                throw new RuntimeException(sprintf('Neplatný env riadok %d.', $lineNumber + 1));
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));
            if ($name === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_.]*$/', $name)) {
                throw new RuntimeException(sprintf('Neplatný názov env premennej na riadku %d.', $lineNumber + 1));
            }

            if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
                || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            if (getenv($name) !== false) {
                continue;
            }

            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }

        self::$loaded = true;
    }

    public static function isLoaded(): bool
    {
        return self::$loaded;
    }
}
