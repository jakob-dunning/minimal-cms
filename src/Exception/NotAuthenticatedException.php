<?php

namespace App\Exception;

use Throwable;

class NotAuthenticatedException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Bad username or password', 410, $previous);
    }
}