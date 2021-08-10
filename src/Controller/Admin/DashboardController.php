<?php

namespace App\Controller\Admin;

use App\Exception\AnonymousUserException;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Model\User\UserInterface;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\Config;
use Twig\Environment;

class DashboardController
{
    public const ADMIN_MENU = [
        ['label' => 'Dashboard', 'target' => '/admin/dashboard'],
        ['label' => 'Users', 'target' => '/admin/user'],
        ['label' => 'Pages', 'target' => '/admin/page'],
    ];

    private Config $config;

    private UserRepository $userRepository;

    private PageRepository $pageRepository;

    private Environment $twig;

    public function __construct(UserRepository $userRepository, PageRepository $pageRepository, Config $config, Environment $twig)
    {
        $this->config         = $config;
        $this->userRepository = $userRepository;
        $this->pageRepository = $pageRepository;
        $this->twig           = $twig;
    }

    public function login(Request $request): ResponseInterface
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated()) {
            return new RedirectResponse('/admin/dashboard');
        }

        if ($request->getMethod() !== Request::METHOD_POST) {
            return new Response(
                $this->twig->render('login.html.twig', ['activeUri' => $request->getUri()])
            );
        }

        $user = $this->userRepository->findByUsername($request->getPost()['user'] ?? '');

        try {
            if (password_verify($request->getPost()['password'], $user->getPassword()) === false) {
                return new Response(
                    $this->twig->render('login.html.twig', ['activeUri' => $request->getUri(), 'errors' => ['Unknown combination of user and password']]),
                    Response::STATUS_UNAUTHORIZED
                );
            }
        } catch (AnonymousUserException $e) {
            return new Response(
                $this->twig->render('login.html.twig', ['activeUri' => $request->getUri(), 'errors' => [$e->getMessage()]]),
                Response::STATUS_UNAUTHORIZED
            );
        }

        $user->setSessionId($request->getSessionId());
        $this->renewSession($user);
        $this->userRepository->persist($user);

        return new RedirectResponse('/admin/dashboard');
    }

    public function logout(Request $request)
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated() === false) {
            return new RedirectResponse('/admin/login');
        }

        $user->setSessionIdExpiresAt(null);
        $user->setSessionId(null);
        $this->userRepository->persist($user);

        return new RedirectResponse('/admin/login');
    }

    public function dashboard(Request $request): ResponseInterface
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated() === false) {
            return new RedirectResponse('/admin/login');
        }

        $this->renewSession($user);
        $this->userRepository->persist($user);

        $users = $this->userRepository->findAll();
        $pages = $this->pageRepository->findAllPages();

        return new Response(
            $this->twig->render('dashboard.html.twig', ['users' => $users, 'pages' => $pages, 'activeUri' => $request->getUri()]),
        );
    }

    private function renewSession(UserInterface $user)
    {
        $sessionExpirationTime = $this->config->getByKey('sessionExpirationTime');
        $user->setSessionIdExpiresAt((new \DateTime())->modify('+' . $sessionExpirationTime . ' minutes'));
    }
}