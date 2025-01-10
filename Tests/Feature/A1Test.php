<?php

declare(strict_types=1);

namespace Phphleb\WebRotorTests\Src\Tests\Feature;

use Phphleb\WebRotorTests\Src\CommandDto;
use Phphleb\WebRotorTests\Src\ConfigTestTemplates;
use Phphleb\WebRotorTests\Src\GroupCommandTrait;
use Phphleb\WebRotorTests\Src\ResponseTestDto;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class A1Test extends TestCase
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