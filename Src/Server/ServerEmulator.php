<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Src\Server;

use Phphleb\Webrotor\Config;
use Phphleb\Webrotor\Src\Exception\WebRotorComplianceException;
use Phphleb\Webrotor\Src\Handler\GuzzlePsr7Creator;
use Phphleb\Webrotor\Src\Handler\NyholmPsr7Creator;
use Phphleb\Webrotor\Src\Storage\InMemoryStorage;
use Phphleb\Webrotor\Src\Storage\SharedMemoryStorage;
use Phphleb\Webrotor\Src\Storage\StorageInterface;
use Phphleb\Webrotor\WebRotor;
use Phphleb\WebrotorTests\Src\ConfigTestTemplates;
use Phphleb\WebrotorTests\Src\ResponseTestDto;
use Psr\Log\NullLogger;

final class ServerEmulator
{
    private const PSR7_HANDLERS = [NyholmPsr7Creator::class, GuzzlePsr7Creator::class];

    /**
     * @var array<string, array<int, array{0: ResponseTestDto, 1: ResponseTestDto}>>
     */
    private $comparisonResults = [];

    /**
     * @var StorageInterface|null
     */
    private $storage = null;

    public function __construct()
    {
        try {
            // If possible, storage in RAM is used.
            $this->storage = new SharedMemoryStorage();
        } catch (WebRotorComplianceException $e) {
            $this->storage = new InMemoryStorage();
        }
    }

    /**
     * Run a group of sequential requests to each HTTP PSR7 implementation.
     * Returns the final overall result of the test with comparison objects.
     *
     * @param callable[] $codeGroup - a group of actions to be performed in each worker cycle.
     * @param ResponseTestDto[] $comparisons - expected set of response results.
     * @param array $config - configuration of the environment of one worker.
     * @param Config|null $serverConfig - optional configuration of the web server itself.
     * @return bool - the general result of comparison with the expected answers.
     */
    public function multipleExecution(
        array $codeGroup,
        array $comparisons,
        array $config = ConfigTestTemplates::EMPTY_HTTP_CONFIG,
        ?Config $serverConfig = null
    ): bool
    {
        $result = true;
        $this->comparisonResults = [];
        $argv = $config['argv']; // Determines whether it is a worker or an HTTP request.
        foreach(self::PSR7_HANDLERS as $psr7Handler) {
           $isSuccess = $this->exec($config, $psr7Handler, $codeGroup, $comparisons, $argv, $serverConfig);
           $result = $result ? $isSuccess : false;
       }
       return $result;
    }

    /**
     * Returns details of the comparison results.
     *
     * @return array<string, array<int, array{0: ResponseTestDto, 1: ResponseTestDto}>>
     */
    public function getComparisonResults(): array
    {
        return $this->comparisonResults;
    }

    /**
     * Executing requests on one variation of HTTP PSR-7
     */
    private function exec(
        array $config,
        string $creatorClass,
        array $codeGroup,
        array $comparisons,
        ?array $arguments,
        ?Config $serverConfig
    ): bool
    {
        $startSessionId = $config['session_id'] ?? '';
        $startSessionName = $config['session_name'] ?? '';
        $startSessionActive = $config['session_active'] ?? false;

        $psr7Creator = new $creatorClass();
        $logger = new NullLogger();
        $sessionManager = new SessionManager($startSessionId, $startSessionName, $startSessionActive);
        $server = (new WebRotor($serverConfig, $logger, ['argv' => $arguments]))
            ->setStorage($this->storage)
            ->setSessionManager($sessionManager)
            ->setOutput(new Output($sessionManager));

        $httpResult = $server->init($psr7Creator);
        if (($config['type'] === ConfigTestTemplates::HTTP) && $httpResult) {
            return $this->compare($creatorClass, [$httpResult], $comparisons);
        }

        $results = [];
        foreach($codeGroup as $code) {
            $result = $server->run($code);
            $results = array_merge($results, $result);
        }
        return $this->compare($creatorClass, $results, $comparisons);
    }

    private function compare(string $type, array $results, array $comparisons): bool
    {
        $result = true;
        foreach($results as $key => $response) {
            if (empty($comparisons[$key])) {
                return false;
            }
            $comparison = $comparisons[$key];
            $this->comparisonResults[$type][] = [$response, $comparison];
            $isSuccess = (array)$comparison === (array)$response;
            $result = $result ? $isSuccess : false;
        }
       return $result;
    }
}