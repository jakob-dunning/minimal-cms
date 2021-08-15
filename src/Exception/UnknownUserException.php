<?php

namespace App\Exception;

use Throwable;

class UnknownUserException extends \Exception implements AuthenticationExceptionInterface
{
    public const MESSAGE = 'Bad username or password';

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 401, $previous);
    }
}