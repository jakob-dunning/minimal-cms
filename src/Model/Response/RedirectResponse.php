<?php

namespace App\Model\Response;

class RedirectResponse implements ResponseInterface
{
    public const STATUS_TEMPORARY = 302;

    private array    $headers;

    private string   $target;

    public function __construct(string $target, int $statusCode = self::STATUS_TEMPORARY)
    {
        $this->headers = ["http/1.1 {$statusCode}", "Location: {$target}"];
        $this->target    = $target;
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