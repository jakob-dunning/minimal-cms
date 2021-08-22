<?php

namespace App\Exception;

use App\Service\Response\Response;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class PageNotFoundException extends \Exception
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, Response::STATUS_UNAUTHORIZED, $previous);
    }
}