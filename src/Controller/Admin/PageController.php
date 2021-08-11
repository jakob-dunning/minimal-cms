<?php

namespace App\Controller\Admin;

use App\Model\Page;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\SessionService;
use App\ValueObject\FlashMessage;
use App\View\AddPage;
use App\View\ListPage;
use Twig\Environment;

class PageController
{
    private PageRepository $pageRepository;

    private UserRepository $userRepository;

    private Environment $twig;

    private AuthenticationService $authentication;
    /**
     * @var SessionService
     */
    private SessionService $sessionService;

    public function __construct(
        PageRepository $pageRepository,
        UserRepository $userRepository,
        Environment $twig,
        AuthenticationService $authentication,
        SessionService $sessionService
    ) {
        $this->pageRepository = $pageRepository;
        $this->userRepository = $userRepository;
        $this->twig           = $twig;
        $this->authentication = $authentication;
        $this->sessionService = $sessionService;
    }

    public function list(Request $request)
    {
        $this->authentication->authenticateUser($request);

        $pages = $this->pageRepository->findAll();

        return new Response(
            $this->twig->render('page/list.html.twig', ['pages' => $pages])
        );
    }

    public function add(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            $post = $request->getPost();

            $this->pageRepository->create($post['uri'], $post['title'], $post['content']);
            $this->sessionService->setFlash(
                new FlashMessage('Page added successfully', FlashMessage::SEVERITY_LEVEL_SUCCESS)
            );

            return new RedirectResponse('/admin/page');
        }

        return new Response(
            $this->twig->render('page/single.html.twig', ['title' => 'Add page'])
        );
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $get = $request->getGet();

        if ($request->getMethod() !== Request::METHOD_POST) {
            $page = $this->pageRepository->findById($get['id']);

            return new Response(
                $this->twig->render('page/single.html.twig', ['title' => 'Edit page', 'page' => $page])
            );
        }

        $post = $request->getPost();
        $this->pageRepository->persist(new Page($get['id'], $post['uri'], $post['title'], $post['content']));

        return new RedirectResponse('/admin/page');
    }

    public function delete(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $get = $request->getGet();
        $this->pageRepository->deleteById($get['id']);

        return new RedirectResponse('/admin/page');
    }
}