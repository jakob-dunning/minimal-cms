<?php

namespace App\Service\Response;

use App\ValueObject\Uri;

class RedirectResponse implements ResponseInterface
{
    public const STATUS_TEMPORARY = 302;

    private array $headers;

    private int $statusCode;

    public function __construct(Uri $target, int $statusCode = self::STATUS_TEMPORARY)
    {
        $this->headers = ["http/1.1 {$statusCode}", "Location: {$target}"];
        $this->statusCode = $statusCode;

    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return '';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}