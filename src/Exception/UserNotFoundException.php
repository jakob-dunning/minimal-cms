<?php

namespace App\Exception;

use App\Service\Response\Response;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class UserNotFoundException extends \Exception
{
    public const MESSAGE = 'User not found';

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, Response::STATUS_UNAUTHORIZED, $previous);
    }
}