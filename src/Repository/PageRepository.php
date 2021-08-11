<?php

namespace App\Repository;

use App\Model\Page;
use App\Service\Database\RelationalDatabaseInterface;

class PageRepository
{
    private RelationalDatabaseInterface $database;

    public function __construct(RelationalDatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findByUri(string $uri): Page
    {
        $data = $this->database->select('*', 'page', ['uri' => $uri]);

        if (count($data) === 0) {
            throw new \Exception('Page not found for route: ' . $uri);
        }

        $pageData = reset($data);

        return new Page($pageData['id'], $pageData['uri'], $pageData['title'], $pageData['content']);
    }

    public function create(string $uri, string $title, string $content): void
    {
        $this->database->insert('page', ['uri' => $uri, 'title' => $title, 'content' => $content]);
    }

    public function findAll(): array
    {
        $data  = $this->database->select('*', 'page', []);
        $pages = [];

        foreach ($data as $pageData) {
            $pages[] = new Page($pageData['id'], $pageData['uri'], $pageData['title'], $pageData['content']);
        }

        return $pages;
    }

    public function findById(int $id): Page
    {
        $data     = $this->database->select('*', 'page', ['id' => $id]);
        $pageData = reset($data);

        return new Page($pageData['id'], $pageData['uri'], $pageData['title'], $pageData['content']);
    }

    public function persist(Page $page): void
    {
        $this->database->update(
            'page',
            ['uri' => $page->getUri(), 'title' => $page->getTitle(), 'content' => $page->getContent()],
            ['id' => $page->getId()]
        );
    }

    public function deleteById(int $id)
    {
        $this->database->delete(
            'page',
            ['id' => $id]
        );
    }
}