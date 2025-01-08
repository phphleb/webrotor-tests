<?php

declare(strict_types=1);

namespace Phphleb\WebRotorTests\Tests\Start;

use Phphleb\WebRotor\Config;
use Phphleb\WebRotorTests\Src\BaseTestServer;
use Phphleb\WebRotorTests\Src\ConfigTestTemplates;
use Phphleb\WebRotorTests\Src\ResponseTestDto;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class A1HealthCheckTest extends TestCase
{
    use BaseTestServer;

    /**
     * Basic request verification without running a worker.
     */
    public function testHttpServerHealthCheck(): void
    {
        $server = $this->getNewServer();

        $code = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $response->getBody()->write('OK');

            return $response;
        };

        $check = [new ResponseTestDto(200, [], 'OK')];

        $config = new Config();

        $config->workerResponseTimeSec = 1;
        $config->workerLifetimeSec = 1;

        $result = $server->multipleExecution([$code], $check, ConfigTestTemplates::EMPTY_HTTP_CONFIG, $config);

        $this->assertTrue($result);
    }

    /**
     * Checking the launch of one request and processing it by the worker.
     */
    public function testWorkerHealthCheck(): void
    {
        $server = $this->getNewServer();

        $code = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $response->getBody()->write('OK');

            return $response;
        };

        $check = [new ResponseTestDto(200, [], 'OK')];

        $config = new Config();

        $config->workerResponseTimeSec = 0;
        $config->workerLifetimeSec = 1;

        $httpResult = $server->multipleExecution([$code], $check, ConfigTestTemplates::EMPTY_HTTP_CONFIG, $config);
        $this->assertTrue($httpResult);

        $workerResult = $server->multipleExecution([$code], $check, ConfigTestTemplates::EMPTY_WORKER_CONFIG, $config);
        $this->assertTrue($workerResult);
    }
}