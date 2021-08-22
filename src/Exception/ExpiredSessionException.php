<?php

namespace App\Exception;

use App\Service\Response\Response;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class ExpiredSessionException extends \Exception implements AuthenticationExceptionInterface
{
    public const MESSAGE = 'You have been logged out due to inactivity';

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, Response::STATUS_UNAUTHORIZED, $previous);
    }
}