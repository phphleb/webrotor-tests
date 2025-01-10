<?php

declare(strict_types=1);

namespace Phphleb\WebrotorTests\Src;

final class ResponseTestDto
{
    /**
     * @var int
     */
   public $code;

    /**
     * @var array
     */
   public $headers;

    /**
     * @var string
     */
   public $body;

   public function __construct(int $code, array $headers, string $body)
   {
       $this->code = $code;
       $this->headers = $headers;
       $this->body = $body;
   }
}