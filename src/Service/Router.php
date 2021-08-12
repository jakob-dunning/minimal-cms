<?php declare(strict_types=1);

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Exception\AuthenticationExceptionInterface;
use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\ResponseInterface;
use App\ValueObject\FlashMessage;
use Twig\Environment;

class Router
{
    private const ROUTING_TABLE = [
        '/admin/login'       => ['dashboardController', 'login'],
        '/admin/logout'      => ['dashboardController', 'logout'],
        '/admin/dashboard'   => ['dashboardController', 'dashboard'],
        '/admin/user'        => ['userController', 'list'],
        '/admin/user/create' => ['userController', 'create'],
        '/admin/user/edit'   => ['userController', 'edit'],
        '/admin/user/delete' => ['userController', 'delete'],
        '/admin/page'        => ['pageController', 'list'],
        '/admin/page/create' => ['pageController', 'create'],
        '/admin/page/edit'   => ['pageController', 'edit'],
        '/admin/page/delete' => ['pageController', 'delete'],
        '/error'             => ['publicController', 'error'],
        '/'                  => ['publicController', 'home'],
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
            if (key_exists($uri, self::ROUTING_TABLE)) {
                $route      = self::ROUTING_TABLE[$uri];
                $controller = $route[0];
                $method     = $route[1];

                return $this->$controller->$method($request);
            }

            return $this->publicController->page($request);
        } catch (AuthenticationExceptionInterface $e) {
            $this->sessionService->addFlash(
                new FlashMessage($e->getMessage(), FlashMessage::ALERT_LEVEL_ERROR)
            );
            return new RedirectResponse('/admin/login');
        } catch (\Throwable $t) {
            die($t->getMessage());

            return new RedirectResponse('/error', $t->getCode());
        }
    }
}