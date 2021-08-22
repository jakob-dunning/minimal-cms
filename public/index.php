<?php declare(strict_types=1);

use App\Service\Request;
use App\Service\Factory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$request = Request::createFromGlobals();
$factory = new Factory($request);
$router  = $factory->createRouter();

$factory->createSessionService()->deleteFlashes();
$response = $router->route($request);

foreach ($response->getHeaders() as $header) {
    header($header);
};

echo $response->getBody();

