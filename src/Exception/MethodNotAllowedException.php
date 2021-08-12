<?php

namespace App\Exception;

use Throwable;

class MethodNotAllowedException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Method not allowed', 405, $previous);
    }
}