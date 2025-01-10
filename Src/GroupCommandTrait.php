<?php

namespace Phphleb\WebrotorTests\Src;

use Phphleb\Webrotor\Config;

trait GroupCommandTrait
{
    use BaseCommandTrait;

    protected $result = [];

    protected function getExecResult(): array
    {
        return $this->result;
    }

    /**
     * @param CommandDto[] $commands
     */
    protected function execCommands(array $commands): bool
    {
        $this->result = [];
        $server = $this->getNewServer();
        $results = [];
        $isSuccess = true;
        foreach ($commands as $key => $command) {
            $config = $command->serverConfig ?? new Config();
            $config->workerResponseTimeSec = 0;
            $config->workerLifetimeSec = 1;
            $onceResult = $server->multipleExecution(
                $command->code,
                $command->comparison,
                $command->envConfig,
                $config
            );
            $isSuccess = $isSuccess && $onceResult;
            $results[$key] = $server->getComparisonResults();
        }
        $this->result = $results;

        return $isSuccess;
    }

}
