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

final class SameCookiesTest extends TestCase
{
    use GroupCommandTrait;

    /**
     * Checking the operation of Cookies through attributes.
     */
    public function testCookiesFromAttributes(): void
    {
        $setCookie = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $request->getAttribute('cookie')->set('TEST-COOKIE-1', 'test-cookie-1');
            $response->getBody()->write('SET');
            return $response;
        };
        $outputCookie = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $cookieParam = $request->getAttribute('cookie')->get('TEST-COOKIE-1');
            $response->getBody()->write((string)$cookieParam);
            return $response;
        };

        $setCheck = new ResponseTestDto(200, [], 'SET');
        $outputCheck = new ResponseTestDto(200, [], 'test-cookie-1');

        $commands = [
            new CommandDto([$setCookie],[$setCheck]),
            new CommandDto([$outputCookie],[$outputCheck], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }

    /**
     * Checking the Cookies setting through an attribute, and receiving through a global array.
     */
    public function testCookiesFromAttributesToGlobals(): void
    {
        $setCookie = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $request->getAttribute('cookie')->set('TEST-COOKIE-2', 'test-cookie-2');
            $response->getBody()->write('SET');
            return $response;
        };
        $outputCookie = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $response->getBody()->write($_COOKIE['TEST-COOKIE-2'] ?? '');
            return $response;
        };

        $setCheck = new ResponseTestDto(200, [], 'SET');
        $outputCheck = new ResponseTestDto(200, [], 'test-cookie-2');

        $commands = [
            new CommandDto([$setCookie],[$setCheck]),
            new CommandDto([$outputCookie],[$outputCheck], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }
}