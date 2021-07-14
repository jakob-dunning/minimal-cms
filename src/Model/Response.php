<?php

namespace App\Model;

class Response
{
    const STATUS_OK = 200;

    private int $statusCode;

    private string $content;

    private function __construct(string $content, int $statusCode)
    {
        $this->content    = $content;
        $this->statusCode = $statusCode;
    }

    public static function createFromString(string $content, $statusCode = self::STATUS_OK): self
    {
        return new self($content, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}