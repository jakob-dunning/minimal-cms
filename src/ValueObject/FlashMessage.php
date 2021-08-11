<?php

namespace App\ValueObject;

class FlashMessage
{
    public const ALERT_LEVEL_SUCCESS = 'success';

    public const ALERT_LEVEL_WARNING = 'warning';

    public const ALERT_LEVEL_ERROR = 'error';

    private string $alertLevel;

    private string $message;

    public function __construct(string $message, string $alertLevel)
    {
        $this->alertLevel = $alertLevel;
        $this->message    = $message;
    }

    public function getAlertLevel(): string
    {
        return $this->alertLevel;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}