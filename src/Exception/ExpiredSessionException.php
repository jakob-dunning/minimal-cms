<?php

namespace App\Exception;

use Throwable;

class ExpiredSessionException extends \Exception implements AuthenticationExceptionInterface
{
    public const MESSAGE = 'You have been logged out due to inactivity';

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 401, $previous);
    }
}