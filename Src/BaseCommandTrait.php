<?php

declare(strict_types=1);

namespace Phphleb\WebRotorTests\Src;

use Phphleb\WebRotorTests\Src\Server\ServerEmulator;

trait BaseCommandTrait
{
    /**
     * @var ServerEmulator|null
     */
   private $server = null;

   protected function getNewServer(): ServerEmulator
   {
       $this->server = new ServerEmulator();

       return $this->server;
   }

   protected function getServer(): ServerEmulator
   {
       return $this->server;
   }
}