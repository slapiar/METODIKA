<?php

declare(strict_types=1);

use App\Services\DatabaseCapabilityInspector;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DatabaseCapabilityInspectorTest extends CIUnitTestCase
{
    public function testInspectReturnsSafeResultWhenConnectionThrows(): void
    {
        $inspector = new DatabaseCapabilityInspector(static function () {
            throw new RuntimeException('connection details should never leak');
        });

        $result = $inspector->inspect();

        $this->assertFalse($result['connection']);
        $this->assertSame('', $result['serverVersion']);
        $this->assertFalse($result['server']);
        $this->assertFalse($result['innodb']);
        $this->assertFalse($result['utf8mb4Bin']);
        $this->assertFalse($result['datetime6']);
        $this->assertSame(DatabaseCapabilityInspector::ERROR_CONNECTION_FAILED, $result['errorCode']);
        $this->assertArrayNotHasKey('exception', $result);
    }
}
