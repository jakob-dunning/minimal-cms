<?php

namespace App\Service;

use App\Model\Page;
use PDO;

class Database
{
    private PDO $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findPageByUri(string $uri): Page
    {
        $statement = $this->pdo->prepare('SELECT * FROM page WHERE uri = :uri');
        $statement->execute([':uri' => $uri]);

        $data = $statement->fetch();
        if($data === false) {
            throw new \Exception('Page not found for route: ' . $uri);
        }

        return new Page($data['uri'], $data['content']);
    }
}