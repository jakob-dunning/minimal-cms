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

    public static function createFromArray($pageData): self
    {
        return new self($pageData['id'], $pageData['uri'], $pageData['title'], $pageData['content']);
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

    public function getId(): int
    {
        return $this->id;
    }

    public function setContent(string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    public function setTitle(string $title) : self
    {
        $this->title = $title;

        return $this;
    }

    public function setUri(string $uri) : self
    {
        $this->uri = $uri;

        return $this;
    }
}