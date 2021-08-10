<?php declare(strict_types=1);

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Exception\AnonymousUserException;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\ResponseInterface;

class Router
{
    private const ADMIN_ROUTING_TABLE = [
        '/admin/login'       => ['dashboardController', 'login'],
        '/admin/logout'      => ['dashboardController', 'logout'],
        '/admin/dashboard'   => ['dashboardController', 'dashboard'],
        '/admin/user'        => ['userController', 'list'],
        '/admin/user/add'    => ['userController', 'add'],
        '/admin/user/edit'   => ['userController', 'edit'],
        '/admin/user/delete' => ['userController', 'delete'],
        '/admin/page'        => ['pageController', 'list'],
        '/admin/page/add'    => ['pageController', 'add'],
        '/admin/page/edit'   => ['pageController', 'edit'],
        '/admin/page/delete' => ['pageController', 'delete'],
    ];

    private PublicController $defaultController;

    private DashboardController $dashboardController;

    private PageController $pageController;

    private UserController $userController;

    public function __construct(
        DashboardController $dashboardController,
        UserController $userController,
        PageController $pageController,
        PublicController $defaultController
    ) {
        $this->defaultController   = $defaultController;
        $this->dashboardController = $dashboardController;
        $this->userController      = $userController;
        $this->pageController      = $pageController;
    }

    public function route(Request $request): ResponseInterface
    {
        $uri = $request->getUri();

        try {
            if (key_exists($uri, self::ADMIN_ROUTING_TABLE)) {
                $route      = self::ADMIN_ROUTING_TABLE[$uri];
                $controller = $route[0];
                $method     = $route[1];

                return $this->$controller->$method($request);
            }
        } catch (AnonymousUserException $e) {
            return new RedirectResponse('/admin/login');
        }

        return $this->defaultController->page($request);
    }
}