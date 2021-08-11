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

    private AuthenticationService $authenticationService;
    /**
     * @var SessionService
     */
    private SessionService $sessionService;

    public function __construct(
        PageRepository $pageRepository,
        UserRepository $userRepository,
        Environment $twig,
        AuthenticationService $authenticationService,
        SessionService $sessionService
    ) {
        $this->pageRepository        = $pageRepository;
        $this->userRepository        = $userRepository;
        $this->twig                  = $twig;
        $this->authenticationService = $authenticationService;
        $this->sessionService        = $sessionService;
    }

    public function list(Request $request)
    {
        $this->authenticationService->authenticateUser($request);

        $pages = $this->pageRepository->findAll();

        return new Response(
            $this->twig->render('page/list.html.twig', ['pages' => $pages])
        );
    }

    public function add(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response(
                $this->twig->render('page/single.html.twig', ['title' => 'Add page'])
            );
        }

        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new \Exception('Unknown method');
        }

        $post = $request->getPost();

        try {
            $this->pageRepository->create($post['uri'], $post['title'], $post['content']);
        } catch (\Throwable $t) {
            $this->sessionService->setFlash(
                new FlashMessage($t->getMessage(), FlashMessage::ALERT_LEVEL_ERROR)
            );

            return new RedirectResponse('/admin/page/add');
        }

        $this->sessionService->setFlash(
            new FlashMessage('Page added successfully', FlashMessage::ALERT_LEVEL_SUCCESS)
        );

        return new RedirectResponse('/admin/page');
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

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
        $this->authenticationService->authenticateUser($request);

        $get = $request->getGet();
        $this->pageRepository->deleteById($get['id']);

        return new RedirectResponse('/admin/page');
    }
}