<?php

declare(strict_types=1);

namespace Phphleb\WebRotorTests\Src;

use Phphleb\WebRotorTests\Src\Server\ServerEmulator;

trait BaseTestServer
{
    /**
     * @var ServerEmulator|null
     */
   private $server = null;

   public function getNewServer(): ServerEmulator
   {
       $this->server = new ServerEmulator();

       return $this->server;
   }

   public function getServer(): ServerEmulator
   {
       return $this->server;
   }
}