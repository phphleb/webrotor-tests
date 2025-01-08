<?php

declare(strict_types=1);

namespace Phphleb\WebRotorTests\Src\Server;

use Phphleb\WebRotor\Src\Session\SessionManagerInterface;

/**
 * @author Foma Tuturov <fomiash@yandex.ru>
 *
 * Working with sessions in separate methods.
 */
final class SessionManager implements SessionManagerInterface
{
    /**
     * @var string
     */
    private $startSessionId;
    /**
     * @var string
     */
    private $startSessionName;

    private $isActive;

    public function __construct(string $startSessionId, string $startSessionName, bool $isActive)
    {
        $this->startSessionId = $startSessionId;
        $this->startSessionName = $startSessionName;
        $this->isActive = $isActive;
    }

    /** @inheritDoc */
    #[\Override]
    public function restart(string $id, string $name): void
    {
        $this->isActive = true;
        $this->startSessionId = $id;
        $this->startSessionName = $name;
    }

    /**
     * @inheritDoc
     *
     * @return array<string, string|false>
     */
    #[\Override]
    public function start(): array
    {
        $this->isActive = true;
        return ['session_id' => $this->startSessionId, 'session_name' => $this->startSessionName];
    }


    /** @inheritDoc */
    #[\Override]
    public function clean(): void
    {
        $this->startSessionId = '';
        $this->startSessionName = '';
        $this->isActive = false;
    }

    /** @inheritDoc */
    #[\Override]
    public function isActive(): bool
    {
        return $this->isActive;
    }
}
