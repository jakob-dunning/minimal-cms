<?php

namespace App\ValueObject;

class Uri
{
    private string $uri;

    private string $path;

    private function __construct(string $uri, string $path)
    {
        $this->uri = $uri;
        $this->path = $path;
    }

    public static function createFromString(string $uri): self
    {
        $parsed = parse_url($uri);

        if ($parsed === false) {
            throw new \Exception("Not a valid uri: {$uri}");
        }

        return new self($uri, $parsed['path']);
    }

    public function __toString(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}