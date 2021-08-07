<?php declare(strict_types=1);

namespace App\Model;

class Page
{
    private string $uri;

    private string $content;

    public function __construct(string $uri = 'g', string $content = 'g')
    {
        $this->uri     = $uri;
        $this->content = $content;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}