<?php declare(strict_types=1);

use App\Model\Request;
use App\Model\Response\Response;
use App\Service\Factory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$request  = Request::createFromGlobals();
$factory = new Factory($request);

try {
    $router   = $factory->createRouter();
    $response = $router->route($request);

    foreach ($response->getHeaders() as $header) {
        header($header);
    };

    echo $response->getBody();
} catch (\Throwable $t) {
    $twig = $factory->createTwig();
    $response = new Response($twig->render('error.html.twig', ['errors' => [$t->getMessage()]]));

    echo $response->getBody();
}