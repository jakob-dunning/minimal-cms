<?php declare(strict_types=1);

namespace App\Model;

class Page
{
    private string $uri;

    private string $content;

    private string $title;

    private int    $id;

    public function __construct(int $id, string $uri, string $title, string $content)
    {
        $this->uri     = $uri;
        $this->content = $content;
        $this->title   = $title;
        $this->id      = $id;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getId() : int
    {
        return $this->id;
    }
}