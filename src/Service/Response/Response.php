<?php

namespace App\Service\Response;

class Response implements ResponseInterface
{
    public const STATUS_OK = 200;

    public const STATUS_UNAUTHORIZED = 401;

    public const STATUS_NOT_ALLOWED = 405;

    private string $content;

    private array $headers = [];

    private int $statusCode;

    public function __construct(string $content, int $statusCode = self::STATUS_OK)
    {
        $this->content    = $content;
        $this->headers[]  = 'http/1.1 ' . $statusCode;
        $this->statusCode = $statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}