<?php

namespace App\Controller;

use App\Model\Request;
use App\Model\Response\Response;
use App\Repository\PageRepository;
use Twig\Environment;

class PublicController
{
    public const MENU = [
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
        $page = $this->pageRepository->findByUri($request->getUri());

        return new Response($this->twig->render('page.html.twig', ['page' => $page]));
    }

    public function error(Request $request):Response {
        return new Response($this->twig->render('error.html.twig'));
    }
}