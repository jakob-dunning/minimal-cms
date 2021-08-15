<?php

namespace App\Service;

class DateTimeService
{
    public function now() : \DateTime {
        return new \DateTime();
    }
}