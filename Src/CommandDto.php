<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Src;

use Phphleb\Webrotor\Config;

class CommandDto
{
    /**
     * List of functions to perform and obtain results.
     *
     * @var callable[]
     */
    public $code;

    /**
     * Environment configuration (global variables, etc.).
     *
     * @var array
     */
    public $envConfig;

    /**
     * @var ?Config
     */
    public $serverConfig = null;

    /**
     * Estimated result for comparison.
     *
     * @var ResponseTestDto[]
     */
    public $comparison;

    /**
     * @param callable[] $code
     * @param array $envConfig
     * @param Config|null $serverConfig
     * @param ResponseTestDto[] $comparison
     */
    public function __construct(
        array $code,
        array $comparison = [],
        array $envConfig = ConfigTestTemplates::EMPTY_HTTP_CONFIG,
        ?Config $serverConfig = null
    )
    {
        $this->code = $code;
        $this->envConfig = $envConfig;
        $this->serverConfig = $serverConfig;
        $this->comparison = $comparison;
    }

}