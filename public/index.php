<?php declare(strict_types=1);

use App\Model\Request;
use App\Service\Factory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$request = Request::createFromGlobals();
$factory = new Factory($request);
$factory->createSessionService()->deleteFlashes();

$router   = $factory->createRouter();
$response = $router->route($request);

foreach ($response->getHeaders() as $header) {
    header($header);
};

echo $response->getBody();