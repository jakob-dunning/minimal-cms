<?php

namespace App\Controller\Admin;

use App\Exception\NotAuthenticatedException;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\Config;
use App\Service\SessionService;
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

    private AuthenticationService $authentication;

    private SessionService $sessionService;

    public function __construct(
        UserRepository $userRepository,
        PageRepository $pageRepository,
        Config $config,
        Environment $twig,
        AuthenticationService $authentication,
        SessionService $sessionService
    ) {
        $this->config         = $config;
        $this->userRepository = $userRepository;
        $this->pageRepository = $pageRepository;
        $this->twig           = $twig;
        $this->authentication = $authentication;
        $this->sessionService = $sessionService;
    }

    public function login(Request $request): ResponseInterface
    {
        if ($request->getMethod() === Request::METHOD_GET) {
            try {
                $this->authentication->authenticateUser($request);
            } catch (NotAuthenticatedException $e) {
                return new Response($this->twig->render('login.html.twig'));
            }

            return new RedirectResponse('/admin/dashboard');
        }

        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new NotAuthenticatedException();
        }

        $user = $this->userRepository->findByUsername($request->getPost()['user'] ?? '');

        if($user === null) {
            throw new NotAuthenticatedException();
        }

        if (password_verify($request->getPost()['password'], $user->getPassword()) === false) {
            throw new NotAuthenticatedException();
        }

        $user->setSessionId($request->getSessionId());
        $this->authentication->renewSession($user);

        $this->userRepository->persist($user);

        return new RedirectResponse('/admin/dashboard');
    }

    public function logout(Request $request)
    {
        $user = $this->authentication->authenticateUser($request);

        $user->setSessionIdExpiresAt(null);
        $user->setSessionId(null);

        $this->userRepository->persist($user);

        return new RedirectResponse('/admin/login');
    }

    public function dashboard(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $users = $this->userRepository->findAll();
        $pages = $this->pageRepository->findAll();

        return new Response(
            $this->twig->render('dashboard.html.twig', ['users' => $users, 'pages' => $pages]),
        );
    }
}