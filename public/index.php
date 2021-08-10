<?php declare(strict_types=1);

use App\Model\Request;
use App\Model\Response\Response;
use App\Service\Factory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

try {
    $request  = Request::createFromGlobals();
    $router   = Factory::createRouter();
    $response = $router->route($request);

    foreach ($response->getHeaders() as $header) {
        header($header);
    };

    echo $response->getBody();
} catch (\Throwable $t) {
    $twig = Factory::createTwig();
    $response = new Response($twig->render('error.html.twig', ['errors' => [$t->getMessage()]]));

    echo $response->getBody();
}