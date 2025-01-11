<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Tests\Start;

use Phphleb\Webrotor\Config;
use Phphleb\WebrotorTests\Src\BaseCommandTrait;
use Phphleb\WebrotorTests\Src\ConfigTestTemplates;
use Phphleb\WebrotorTests\Src\ResponseTestDto;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HealthCheckTest extends TestCase
{
    use BaseCommandTrait;

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