<?php

namespace App\Controller;

use App\Service\Request;
use App\Service\Response\Response;
use App\Repository\PageRepository;
use Twig\Environment;

/**
 * @codeCoverageIgnore
 */
class PublicController
{
    public const MENU = [
        ['label' => 'Home', 'target' => '/'],
        ['label' => 'Login', 'target' => '/admin/login'],
    ];

    private PageRepository $pageRepository;

    private Environment $twig;

    public function __construct(PageRepository $database, Environment $twig)
    {
        $this->pageRepository = $database;
        $this->twig           = $twig;
    }

    public function page(Request $request): Response
    {
        $page = $this->pageRepository->findByPath($request->getUri()->getPath());

        return new Response($this->twig->render('page.html.twig', ['page' => $page]));
    }

    public function home(): Response
    {
        $pages = $this->pageRepository->findAll();

        return new Response($this->twig->render('home.html.twig', ['pages' => $pages]));
    }

    public function error(): Response
    {
        return new Response($this->twig->render('error.html.twig'));
    }
}