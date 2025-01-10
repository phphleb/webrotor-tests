<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Src;

class ConfigTestTemplates
{
    const HTTP = 'http';
    const WORKER = 'worker';

    const EMPTY_HTTP_CONFIG = [
        'type' => self::HTTP,
        'argv' => null,   // (array|null) if it's an array, then a worker is used.
        'get' => [],      // $_GET server emulation.
        'post' => [],     // $_POST server emulation.
        'cookie' => [],   // $_COOKIE server emulation.
        'session' => [],  // $_SESSION server emulation.
        'env' => [],      // $_ENV server emulation.
        'server' => [],   // $_SERVER server emulation.
    ];

    const EMPTY_WORKER_CONFIG = [
        'type' => self::WORKER,
        'argv' => [],     // (array|null) if it's an array, then a worker is used.
        'get' => [],      // $_GET server emulation.
        'post' => [],     // $_POST server emulation.
        'cookie' => [],   // $_COOKIE server emulation.
        'session' => [],  // $_SESSION server emulation.
        'env' => [],      // $_ENV server emulation.
        'server' => [],   // $_SERVER server emulation.
    ];
}