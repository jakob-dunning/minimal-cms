<?php declare(strict_types=1);

use App\Model\Request;
use App\Service\Factory;

require __DIR__ . '/../vendor/autoload.php';

try {
    $request = Request::createFromGlobals();
    $router  = Factory::createRouter();
    $response = $router->route($request);

    foreach ($response->getHeaders() as $header) {
        header($header);
    };

    echo $response->getBody();
} catch (\Throwable $t) {
    echo $t->getMessage();
}