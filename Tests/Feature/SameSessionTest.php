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

final class SameSessionTest extends TestCase
{
    use GroupCommandTrait;

    /**
     * Checking the session operation through a global array (by default, the session id is the same).
     */
    public function testSessionFromGlobals(): void
    {
        $setSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $_SESSION['TEST-SESSION-1'] = 'test-session-1';
            $response->getBody()->write('SET');
            return $response;
        };
        $outputSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $response->getBody()->write($_SESSION['TEST-SESSION-1'] ?? '');
            return $response;
        };

        $setCheck = new ResponseTestDto(200, [], 'SET');
        $outputCheck = new ResponseTestDto(200, [], 'test-session-1');

        $commands = [
            new CommandDto([$setSession],[$setCheck]),
            new CommandDto([$outputSession],[$outputCheck], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }

    /**
     * Checking the operation of sessions through attributes.
     */
    public function testSessionFromAttributes(): void
    {
        $setSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $request->getAttribute('session')->set('TEST-SESSION-2', 'test-session-2');
            $response->getBody()->write('SET');
            return $response;
        };
        $outputSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $sessionParam = $request->getAttribute('session')->get('TEST-SESSION-2');
            $response->getBody()->write((string)$sessionParam);
            return $response;
        };

        $setCheck = new ResponseTestDto(200, [], 'SET');
        $outputCheck = new ResponseTestDto(200, [], 'test-session-2');

        $commands = [
            new CommandDto([$setSession],[$setCheck]),
            new CommandDto([$outputSession],[$outputCheck], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }

    /**
     * Checking the session setting through an attribute, and receiving through a global array.
     */
    public function testSessionFromAttributesToGlobals(): void
    {
        $setSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $request->getAttribute('session')->set('TEST-SESSION-3', 'test-session-3');
            $response->getBody()->write('SET');
            return $response;
        };
        $outputSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $response->getBody()->write($_SESSION['TEST-SESSION-3'] ?? '');
            return $response;
        };

        $setCheck = new ResponseTestDto(200, [], 'SET');
        $outputCheck = new ResponseTestDto(200, [], 'test-session-3');

        $commands = [
            new CommandDto([$setSession],[$setCheck]),
            new CommandDto([$outputSession],[$outputCheck], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }

    /**
     * Checking the session setting through a global array, and receiving through attributes.
     */
    public function testSessionFromGlobalsToAttributes(): void
    {
        $setSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $_SESSION['TEST-SESSION-4'] = 'test-session-4';
            $response->getBody()->write('SET');
            return $response;
        };
        $outputSession = static function(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $sessionParam = $request->getAttribute('session')->get('TEST-SESSION-4');
            $response->getBody()->write((string)$sessionParam);
            return $response;
        };

        $setCheck = new ResponseTestDto(200, [], 'SET');
        $outputCheck = new ResponseTestDto(200, [], 'test-session-4');

        $commands = [
            new CommandDto([$setSession],[$setCheck]),
            new CommandDto([$outputSession],[$outputCheck], ConfigTestTemplates::EMPTY_WORKER_CONFIG)
        ];

        $this->assertTrue($this->execCommands($commands));
    }
}