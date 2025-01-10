<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Src\Server;

use Phphleb\WebrotorTests\Src\ResponseTestDto;

/**
 * @author Foma Tuturov <fomiash@yandex.ru>
 *
 * Implements the ability to override data output.
 */
final class Output extends \Phphleb\Webrotor\Src\Process\Output
{
    /**
     * @var ResponseTestDto[]
     */
    private $results = [];

    /**
     * Can return an array with response data
     * when overridden, or an empty array.
     *
     * @return array<int, ResponseTestDto>
     */
    #[\Override]
    public function getResult(): array
    {
        return $this->results;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function run(array $response): void
    {
         $this->sessionManager->restart(
            (string)($response['middleware']['session']['sessionId']),
            (string)($response['middleware']['session']['sessionName']));

        $this->results[] = new ResponseTestDto($response['statusCode'], $response['headers'], $response['body']);
    }
}
