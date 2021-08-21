<?php

namespace App\Model\Response;

use App\ValueObject\Uri;

class RedirectResponse implements ResponseInterface
{
    public const STATUS_TEMPORARY = 302;

    private array $headers;

    public function __construct(Uri $target, int $statusCode = self::STATUS_TEMPORARY)
    {
        $this->headers = ["http/1.1 {$statusCode}", "Location: {$target}"];
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return '';
    }
}