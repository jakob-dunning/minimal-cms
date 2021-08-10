<?php

namespace App\Controller\Admin;

use App\Model\Page;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\View\AddPage;
use App\View\ListPage;
use Twig\Environment;

class PageController
{
    private PageRepository $pageRepository;

    private UserRepository $userRepository;

    private Environment $twig;

    public function __construct(PageRepository $pageRepository, UserRepository $userRepository, Environment $twig)
    {
        $this->pageRepository = $pageRepository;
        $this->userRepository = $userRepository;
        $this->twig           = $twig;
    }

    public function list(Request $request)
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated() === false) {
            return new RedirectResponse('/admin/login');
        }

        $pages = $this->pageRepository->findAllPages();

        return new Response(
            $this->twig->render('page/list.html.twig', ['pages' => $pages, 'activeUri' => $request->getUri()])
        );
    }

    public function add(Request $request): ResponseInterface
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated() === false) {
            return new RedirectResponse('/admin/login');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $post = $request->getPost();

            $this->pageRepository->createPage($post['uri'], $post['title'], $post['content']);

            return new RedirectResponse('/admin/page');
        }

        return new Response(
            $this->twig->render('page/single.html.twig', ['title' => 'Add page', 'activeUri' => $request->getUri()])
        );
    }

    public function edit(Request $request): ResponseInterface
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated() === false) {
            return new RedirectResponse('/admin/login');
        }

        $get = $request->getGet();

        if ($request->getMethod() !== Request::METHOD_POST) {
            $page = $this->pageRepository->findById($get['id']);

            return new Response(
                $this->twig->render('page/single.html.twig', ['title' => 'Edit page', 'page' => $page, 'activeUri' => $request->getUri()])
            );
        }

        $post = $request->getPost();

        $this->pageRepository->persist(new Page($get['id'], $post['uri'], $post['title'], $post['content']));

        return new RedirectResponse('/admin/page');
    }

    public function delete(Request $request): ResponseInterface
    {
        $get = $request->getGet();

        $this->pageRepository->deleteById($get['id']);

        return new RedirectResponse('/admin/page');
    }
}