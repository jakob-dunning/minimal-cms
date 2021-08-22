<?php

namespace App\Controller\Admin;

use App\Exception\AuthenticationExceptionInterface;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\Config;
use App\Service\Request;
use App\Service\Response\RedirectResponse;
use App\Service\Response\Response;
use App\Service\Response\ResponseInterface;
use App\Service\Session;
use App\ValueObject\Uri;
use Twig\Environment;
use function var_dump;

class DashboardController
{
    public const ADMIN_MENU = [
        ['label' => 'Home', 'target' => '/'],
        ['label' => 'Dashboard', 'target' => '/admin/dashboard'],
        ['label' => 'Users', 'target' => '/admin/user'],
        ['label' => 'Pages', 'target' => '/admin/page'],
        ['label' => 'Logout', 'target' => '/admin/logout'],
    ];

    private Config $config;

    private UserRepository $userRepository;

    private PageRepository $pageRepository;

    private Environment $twig;

    private AuthenticationService $authenticationService;

    private Session $sessionService;

    public function __construct(
        UserRepository $userRepository,
        PageRepository $pageRepository,
        Config $config,
        Environment $twig,
        AuthenticationService $authenticationService,
        Session $sessionService
    ) {
        $this->config                = $config;
        $this->userRepository        = $userRepository;
        $this->pageRepository        = $pageRepository;
        $this->twig                  = $twig;
        $this->authenticationService = $authenticationService;
        $this->sessionService        = $sessionService;
    }

    public function login(Request $request): ResponseInterface
    {
        if ($request->getMethod() === Request::METHOD_GET) {
            try {
                $this->authenticationService->authenticateUser($request);
            } catch (AuthenticationExceptionInterface $e) {
                return new Response($this->twig->render('login.html.twig'));
            }

            return new RedirectResponse(Uri::createFromString('/admin/dashboard'));
        }

        try {
            $user = $this->userRepository->findByUsername($request->post()['user'] ?? '');
        } catch (UserNotFoundException $e) {
            throw new NotAuthenticatedException();
        }

        if ($this->authenticationService->verifyPassword($request->post()['password'], $user->getPassword()) === false) {
            throw new NotAuthenticatedException();
        }

        $this->authenticationService->renewSessionId($user);
        $this->authenticationService->updateSessionExpiration($user);

        return new RedirectResponse(Uri::createFromString('/admin/dashboard'));
    }

    public function logout(Request $request): ResponseInterface
    {
        $user = $this->authenticationService->authenticateUser($request);

        $user->setSessionExpiresAt(null);
        $user->setSessionId(null);
        $this->userRepository->persist($user);
        $this->sessionService->destroy();

        return new RedirectResponse(Uri::createFromString('/admin/login'));
    }

    public function dashboard(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $users = $this->userRepository->findAll();
        $pages = $this->pageRepository->findAll();

        return new Response($this->twig->render('dashboard.html.twig', ['users' => $users, 'pages' => $pages]),);
    }
}