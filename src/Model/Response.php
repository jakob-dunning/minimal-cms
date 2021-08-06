<?php

namespace App\Model;

class Response
{
    const STATUS_OK = 200;

    private string $content;

    private array $headers = [];

    public function __construct(string $content, int $statusCode = self::STATUS_OK)
    {
        $this->content   = $content;
        $this->headers[] = 'http/1.1 ' . $statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->content;
    }
}