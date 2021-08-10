<?php

namespace App\Exception;

use Throwable;

class AnonymousUserException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Not logged in', 410, $previous);
    }
}