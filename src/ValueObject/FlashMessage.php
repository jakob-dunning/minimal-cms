<?php

namespace App\ValueObject;

class FlashMessage
{
    public const ALERT_LEVEL_SUCCESS = 'success';

    public const ALERT_LEVEL_WARNING = 'warning';

    public const ALERT_LEVEL_ERROR = 'error';

    public const ALERT_LEVELS = [
        self::ALERT_LEVEL_SUCCESS,
        self::ALERT_LEVEL_WARNING,
        self::ALERT_LEVEL_ERROR,
    ];

    private string $alertLevel;

    private string $message;

    private function __construct(string $message, string $alertLevel)
    {
        $this->alertLevel = $alertLevel;
        $this->message    = $message;
    }

    public static function createFromParameters(string $message, string $alertLevel)
    {
        if (in_array($alertLevel, self::ALERT_LEVELS) === false) {
            throw new \Exception("Not a valid alert level: {$alertLevel}");
        }

        return new self($message, $alertLevel);
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