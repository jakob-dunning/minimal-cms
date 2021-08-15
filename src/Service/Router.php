<?php declare(strict_types=1);

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Exception\AuthenticationExceptionInterface;
use App\Exception\MethodNotAllowedException;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\ResponseInterface;
use App\ValueObject\FlashMessage;
use Twig\Environment;

class Router
{
    private const ROUTING_TABLE = [
        '/admin/login'       => ['dashboardController', 'login', [Request::METHOD_GET, Request::METHOD_POST]],
        '/admin/logout'      => ['dashboardController', 'logout', [Request::METHOD_GET]],
        '/admin/dashboard'   => ['dashboardController', 'dashboard', [Request::METHOD_GET]],
        '/admin/user'        => ['userController', 'list', [Request::METHOD_GET]],
        '/admin/user/create' => ['userController', 'create', [Request::METHOD_GET, Request::METHOD_POST]],
        '/admin/user/edit'   => ['userController', 'edit', [Request::METHOD_GET, Request::METHOD_POST]],
        '/admin/user/delete' => ['userController', 'delete', [Request::METHOD_GET]],
        '/admin/page'        => ['pageController', 'list', [Request::METHOD_GET]],
        '/admin/page/create' => ['pageController', 'create', [Request::METHOD_GET, Request::METHOD_POST]],
        '/admin/page/edit'   => ['pageController', 'edit', [Request::METHOD_GET, Request::METHOD_POST]],
        '/admin/page/delete' => ['pageController', 'delete', [Request::METHOD_GET]],
        '/error'             => ['publicController', 'error', [Request::METHOD_GET]],
        '/'                  => ['publicController', 'home', [Request::METHOD_GET]],
    ];

    private PublicController $publicController;

    private DashboardController $dashboardController;

    private PageController $pageController;

    private UserController $userController;

    private SessionService $sessionService;

    private Environment $twig;

    public function __construct(
        DashboardController $dashboardController,
        UserController $userController,
        PageController $pageController,
        PublicController $publicController,
        SessionService $sessionService
    ) {
        $this->publicController    = $publicController;
        $this->dashboardController = $dashboardController;
        $this->userController      = $userController;
        $this->pageController      = $pageController;
        $this->sessionService      = $sessionService;
    }

    public function route(Request $request): ResponseInterface
    {
        $uri = $request->getUri();

        try {
            if (key_exists($uri, self::ROUTING_TABLE) === false) {
                return $this->publicController->page($request);
            }

            $route             = self::ROUTING_TABLE[$uri];
            $controller        = $route[0];
            $method            = $route[1];
            $allowedHttpMethods = $route[2];

            if(in_array($request->getMethod(), $allowedHttpMethods) === false) {
                throw new MethodNotAllowedException();
            }

            return $this->$controller->$method($request);
        } catch (AuthenticationExceptionInterface $e) {
            $this->sessionService->addFlash(
                new FlashMessage($e->getMessage(), FlashMessage::ALERT_LEVEL_ERROR)
            );
            return new RedirectResponse('/admin/login');
        } catch (\Throwable $t) {
            return new RedirectResponse('/error', $t->getCode());
        }
    }
}