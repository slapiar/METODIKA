<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Throwable;

final class DatabaseCapabilityInspector
{
    public const ERROR_CONNECTION_FAILED = 'CONNECTION_FAILED';
    public const ERROR_QUERY_FAILED = 'QUERY_FAILED';
    public const ERROR_CAPABILITY_MISSING = 'CAPABILITY_MISSING';

    /** @var Closure */
    private Closure $connector;

    public function __construct(?Closure $connector = null)
    {
        $this->connector = $connector ?? static fn () => db_connect('default');
    }

    /**
     * @return array{
     *   connection: bool,
     *   serverVersion: string,
     *   server: bool,
     *   innodb: bool,
     *   utf8mb4Bin: bool,
     *   datetime6: bool,
     *   errorCode: null|'CONNECTION_FAILED'|'QUERY_FAILED'|'CAPABILITY_MISSING',
     *   diagnosedAt: string
     * }
     */
    public function inspect(): array
    {
        $result = [
            'connection' => false,
            'serverVersion' => '',
            'server' => false,
            'innodb' => false,
            'utf8mb4Bin' => false,
            'datetime6' => false,
            'errorCode' => null,
            'diagnosedAt' => gmdate('c'),
        ];

        try {
            $db = ($this->connector)();
            $db->initialize();
            $result['connection'] = true;
        } catch (Throwable) {
            $result['errorCode'] = self::ERROR_CONNECTION_FAILED;
            return $result;
        }

        try {
            $version = $db->query('SELECT VERSION() AS server_version')->getRowArray();
            $engine = $db->query("SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'")->getRowArray();
            $collation = $db->query("SHOW COLLATION LIKE 'utf8mb4_bin'")->getRowArray();
            $datetime = $db->query("SELECT CAST('2026-01-01 00:00:00.123456' AS DATETIME(6)) AS datetime_6")->getRowArray();
        } catch (Throwable) {
            $result['errorCode'] = self::ERROR_QUERY_FAILED;
            return $result;
        }

        $result['serverVersion'] = is_array($version) && isset($version['server_version'])
            ? (string) $version['server_version']
            : '';

        $result['server'] = $result['serverVersion'] !== '';

        $result['innodb'] = is_array($engine)
            && in_array(strtoupper((string) ($engine['SUPPORT'] ?? '')), ['YES', 'DEFAULT'], true);

        $result['utf8mb4Bin'] = is_array($collation) && $collation !== [];

        $result['datetime6'] = is_array($datetime)
            && str_ends_with((string) ($datetime['datetime_6'] ?? ''), '.123456');

        if (! $result['server'] || ! $result['innodb'] || ! $result['utf8mb4Bin'] || ! $result['datetime6']) {
            $result['errorCode'] = self::ERROR_CAPABILITY_MISSING;
        }

        return $result;
    }
}
