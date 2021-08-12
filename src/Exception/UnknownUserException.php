<?php

namespace App\Exception;

use Throwable;

class UnknownUserException extends \Exception implements AuthenticationExceptionInterface
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Bad username or password', 401, $previous);
    }
}