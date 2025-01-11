<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Src\Tests\Feature;

use Phphleb\WebrotorTests\Src\CommandDto;
use Phphleb\WebrotorTests\Src\ConfigTestTemplates;
use Phphleb\WebrotorTests\Src\GroupCommandTrait;
use Phphleb\WebrotorTests\Src\ResponseTestDto;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ActiveServerTest extends TestCase
{
    use GroupCommandTrait;

    public function testDefaultStorage(): void
    {
        $code = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $response->getBody()->write('OK');

            return $response;
        };
        $check = new ResponseTestDto(200, [], 'OK');

        $commands = [
            new CommandDto([$code],[$check]),
            new CommandDto([$code],[$check], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }
}