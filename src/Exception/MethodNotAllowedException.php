<?php

namespace App\Exception;

use App\Service\Response\Response;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class MethodNotAllowedException extends \Exception
{
    public const MESSAGE = 'Method not allowed';

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, Response::STATUS_NOT_ALLOWED, $previous);
    }
}