<?php

namespace App\Controller\Admin;

use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\SessionService;
use App\ValueObject\FlashMessage;
use App\ValueObject\Uri;
use Twig\Environment;

class PageController
{
    private PageRepository $pageRepository;

    private UserRepository $userRepository;

    private Environment $twig;

    private AuthenticationService $authenticationService;

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

    public function list(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $pages = $this->pageRepository->findAll();

        return new Response($this->twig->render('page/list.html.twig', ['pages' => $pages]));
    }

    public function create(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response($this->twig->render('page/single.html.twig', ['title' => 'Add page']));
        }

        $post = $request->post();

        try {
            $this->pageRepository->create($post['uri'], $post['title'], $post['content']);
        } catch (\Throwable $t) {
            $this->sessionService->addFlash(
                FlashMessage::createFromParameters($t->getMessage(), FlashMessage::ALERT_LEVEL_ERROR)
            );

            return new RedirectResponse(Uri::createFromString('/admin/page/create'));
        }

        $this->sessionService->addFlash(FlashMessage::createFromParameters('Page added successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/page'));
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $get  = $request->get();
        $page = $this->pageRepository->findById($get['id']);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response($this->twig->render('page/single.html.twig', ['title' => 'Edit page', 'page' => $page, 'formTarget'=> "/admin/page/edit?id={$page->getId()}"]));
        }

        $pageData = $request->post();
        $page->setContent($pageData['content'])
             ->setTitle($pageData['title'])
             ->setUri(Uri::createFromString($pageData['uri']));
        $this->pageRepository->persist($page);
        $this->sessionService->addFlash(FlashMessage::createFromParameters('Page edited successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/page'));
    }

    public function delete(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $get = $request->get();
        $this->pageRepository->deleteById($get['id']);
        $this->sessionService->addFlash(FlashMessage::createFromParameters('Page deleted successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/page'));
    }
}