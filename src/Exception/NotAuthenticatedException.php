<?php

namespace App\Exception;

use App\Model\Response\Response;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class NotAuthenticatedException extends \Exception implements AuthenticationExceptionInterface
{
    public const MESSAGE = 'Bad username or password';

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, Response::STATUS_UNAUTHORIZED, $previous);
    }
}