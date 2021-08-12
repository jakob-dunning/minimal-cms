<?php

namespace App\Exception;

use Throwable;

class ExpiredSessionException extends \Exception implements AuthenticationExceptionInterface
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('You have been logged out due to inactivity', 401, $previous);
    }
}