<?php

namespace App\Exception;

use Throwable;

class AnonymousUserException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Unknown combination of user and password', 410, $previous);
    }
}