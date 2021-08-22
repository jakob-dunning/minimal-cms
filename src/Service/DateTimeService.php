<?php

namespace App\Service;

/**
 * @codeCoverageIgnore
 */
class DateTimeService
{
    public const FORMAT_SQL = 'Y-m-d H:i:s';

    public function now() : \DateTime {
        return new \DateTime();
    }
}