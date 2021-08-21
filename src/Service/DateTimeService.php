<?php

namespace App\Service;

/**
 * @codeCoverageIgnore
 */
class DateTimeService
{
    public function now() : \DateTime {
        return new \DateTime();
    }
}