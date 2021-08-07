<?php declare(strict_types=1);

namespace App\Service;

use App\Model\Request;
use App\Model\Response;

class Router
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function route(Request $request) : Response
    {
        $page = $this->database->findPageByUri($request->getUri());

        return new Response($page->getContent());
    }
}