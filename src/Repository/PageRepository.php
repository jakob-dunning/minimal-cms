<?php

namespace App\Repository;

use App\Entity\Page;
use App\Exception\PageNotFoundException;
use App\Service\Database\RelationalDatabaseInterface;

class PageRepository
{
    private RelationalDatabaseInterface $database;

    public function __construct(RelationalDatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findByPath(string $path): Page
    {
        $data = $this->database->select(['*'], 'page', ['uri' => $path]);

        if (count($data) === 0) {
            throw new PageNotFoundException("Page not found for path: {$path}");
        }

        $pageData = reset($data);

        return Page::createFromArray($pageData);
    }

    public function create(string $uri, string $title, string $content): void
    {
        $this->database->insert('page', ['uri' => $uri, 'title' => $title, 'content' => $content]);
    }

    public function findAll(): array
    {
        $data  = $this->database->select(['*'], 'page');
        $pages = [];

        foreach ($data as $pageData) {
            $pages[] = Page::createFromArray($pageData);
        }

        return $pages;
    }

    public function findById(int $id): Page
    {
        $data     = $this->database->select(['*'], 'page', ['id' => $id]);

        if(count($data) === 0) {
            throw new PageNotFoundException("Page not found for id: {$id}");
        }

        $pageData = reset($data);

        return Page::createFromArray($pageData);
    }

    public function persist(Page $page): void
    {
        $this->database->update(
            'page',
            ['uri' => $page->getUri()->__toString(), 'title' => $page->getTitle(), 'content' => $page->getContent()],
            ['id' => $page->getId()]
        );
    }

    public function deleteById(int $id) : void
    {
        $this->database->delete(
            'page',
            ['id' => $id]
        );
    }
}