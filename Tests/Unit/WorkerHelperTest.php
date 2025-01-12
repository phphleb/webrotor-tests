<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phphleb\Webrotor\Src\Exception\WebRotorException;
use Phphleb\Webrotor\Src\Process\WorkerHelper;

final class WorkerHelperTest extends TestCase
{
    public function testSortRawKeys(): void
    {
        $requestKeys = ['1-key1', '2-key2', '1-key3'];
        $responseKeys = ['2-key4', '1-key5', '1-key6'];
        $id = 1;

        $result = WorkerHelper::sortRawKeys($requestKeys, $responseKeys, $id);

        $this->assertSame([['1-key1', '1-key3'], ['1-key5', '1-key6']], $result);
    }

    public function testExtractWorkerDataValidData(): void
    {
        $json = json_encode(['start' => 123.45, 'lifetime' => 60, 'version' => 1]);

        $result = WorkerHelper::extractWorkerData($json);

        $this->assertSame(['start' => 123.45, 'lifetime' => 60, 'version' => 1], $result);
    }

    public function testExtractWorkerDataEmptyJson(): void
    {
        $this->expectException(WebRotorException::class);

        WorkerHelper::extractWorkerData('');
    }

    public function testExtractWorkerDataInvalidFormat(): void
    {
        $json = json_encode(['invalid' => 'data']);
        $this->expectException(WebRotorException::class);

        WorkerHelper::extractWorkerData($json);
    }

    public function testCheckIsOlderValid(): void
    {
        $key = '1-1660000000-10-1-x1000c500';
        $type = 'worker-type';
        $startTime = 1660000010.0;

        $result = WorkerHelper::checkIsOlder($key, $type, $startTime);

        $this->assertTrue($result);
    }

    public function testCheckIsOlderVersionValid(): void
    {
        $key = '1-1660000000-10-12-x1000c500';
        $type = 'worker-type';
        $startTime = 1660000001.0;

        $result = WorkerHelper::checkIsOlder($key, $type, $startTime, 12);

        $this->assertFalse($result);
    }

    public function testCheckIsOlderInvalidVersion(): void
    {
        $key = '1-1660000001-10-3-x1000c500';
        $type = 'worker-type';
        $startTime = 1660000000.0;

        $result = WorkerHelper::checkIsOlder($key, $type, $startTime, 12);

        $this->assertTrue($result);
    }

    public function testCheckIsOlderInvalidType(): void
    {
        $key = '1-1660000000-10-1-x1000c500';
        $type = 'worker';
        $startTime = 1660000010.0;

        $result = WorkerHelper::checkIsOlder($key, $type, $startTime);

        $this->assertFalse($result);
    }

    public function testNormalizePath(): void
    {
        $path = '/path/../to/./some/file.txt';

        $result = WorkerHelper::normalizePath($path);

        $this->assertSame('/to/some/file.txt', $result);
    }

    public function testNormalizePathBackslashes(): void
    {
        $path = '\\path\\to\\some\\file.txt';

        $result = WorkerHelper::normalizePath($path);

        $this->assertSame('/path/to/some/file.txt', $result);
    }
}
