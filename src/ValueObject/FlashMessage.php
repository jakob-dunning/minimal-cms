<?php

namespace App\ValueObject;

class FlashMessage
{
    public const SEVERITY_LEVEL_SUCCESS = 'success';

    public const SEVERITY_LEVEL_WARNING = 'warning';

    public const SEVERITY_LEVEL_ERROR = 'error';

    private string $severityLevel;

    private string $message;

    public function __construct(string $message, string $severityLevel)
    {
        $this->severityLevel = $severityLevel;
        $this->message       = $message;
    }

    public function getSeverityLevel(): string
    {
        return $this->severityLevel;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}