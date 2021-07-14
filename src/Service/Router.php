<?php declare(strict_types=1);

namespace App\Service;

use App\Model\Request;
use App\Model\Response;

class Router
{
    public function route(Request $request) {
        $response = Response::createFromString($request->getRequestUri());

        header('HTTP/1.1 ' . $response->getStatusCode());

        echo $response->getContent();
    }
}