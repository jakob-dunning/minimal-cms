<?php declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Uri;

/**
 * @codeCoverageIgnore
 */
class Page
{
    private Uri $uri;

    private string $content;

    private string $title;

    private int $id;

    private function __construct(int $id, Uri $uri, string $title, string $content)
    {
        $this->uri     = $uri;
        $this->content = $content;
        $this->title   = $title;
        $this->id      = $id;
    }

    public static function createFromArray($pageData): self
    {
        return new self($pageData['id'], Uri::createFromString($pageData['uri']), $pageData['title'], $pageData['content']);
    }

    public function getUri(): Uri
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

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setUri(Uri $uri): self
    {
        $this->uri = $uri;

        return $this;
    }
}