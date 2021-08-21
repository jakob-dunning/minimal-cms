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
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\ValueObject\FlashMessage;
use App\ValueObject\RouteList;
use App\ValueObject\Uri;
use Twig\Environment;

class Router
{
    private PublicController $publicController;

    private DashboardController $dashboardController;

    private PageController $pageController;

    private UserController $userController;

    private SessionService $sessionService;

    private Config $config;

    private Environment $environment;

    private RouteList $routes;

    public function __construct(
        DashboardController $dashboardController,
        UserController $userController,
        PageController $pageController,
        PublicController $publicController,
        SessionService $sessionService,
        Config $config,
        Environment $environment,
        RouteList $routes
    ) {
        $this->publicController    = $publicController;
        $this->dashboardController = $dashboardController;
        $this->userController      = $userController;
        $this->pageController      = $pageController;
        $this->sessionService      = $sessionService;
        $this->config              = $config;
        $this->environment         = $environment;
        $this->routes              = $routes;
    }

    public function route(Request $request): ResponseInterface
    {
        $route = $this->routes->findByPath($request->getUri()->getPath());

        try {
            if ($route === null) {
                return $this->publicController->page($request);
            }

            if ($route->getAllowedRequestMethods()->contains($request->getMethod()) === false) {
                throw new MethodNotAllowedException();
            }

            $controller = $route->getController();
            $method     = $route->getMethod();

            return $this->$controller->$method($request);
        } catch (AuthenticationExceptionInterface $e) {
            $this->sessionService->addFlash(FlashMessage::createFromParameters($e->getMessage(), FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString('/admin/login'));
        } catch (\Throwable $t) {
            if ($this->config->getByKey('environment') === 'DEV') {
                return new Response($this->environment->render(
                    'debug.html.twig',
                    ['message' => $t->getMessage(), 'trace' => $t->getTraceAsString()],
                ), $t->getCode());
            }

            return new RedirectResponse(Uri::createFromString('/error'), $t->getCode());
        }
    }
}