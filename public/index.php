<?php declare(strict_types=1);

use App\Model\Request;
use App\Service\Router;

require __DIR__ . '/../vendor/autoload.php';

try {
    $request = Request::createFromGlobals();
    $router  = new Router();
    $router->route($request);
} catch (\Throwable $t) {
    echo $t->getMessage();
}