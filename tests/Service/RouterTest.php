<?php

namespace Test\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Exception\NotAuthenticatedException;
use App\Service\Request;
use App\Service\Response\RedirectResponse;
use App\Service\Response\Response;
use App\Service\Config;
use App\Service\Router;
use App\Service\Session;
use App\ValueObject\RequestMethodList;
use App\ValueObject\Route;
use App\ValueObject\RouteList;
use App\ValueObject\Uri;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

/**
 * @covers \App\Service\Router
 * @uses   \App\Controller\Admin\DashboardController
 * @uses   \App\Controller\Admin\PageController
 * @uses   \App\Controller\Admin\UserController
 * @uses   \App\Controller\Admin\DashboardController
 * @uses   \App\Controller\PublicController
 * @uses   \App\Service\Session
 * @uses   \App\ValueObject\RouteList
 * @uses   \App\Service\FileLoader\JsonFileLoader
 * @uses   \App\ValueObject\RequestMethodList
 * @uses   \App\ValueObject\Route
 * @uses   \App\ValueObject\Uri
 * @uses   \App\Service\Request
 * @uses   \App\Service\Response\Response
 * @uses   \App\Service\Config
 * @uses   \App\Service\Response\RedirectResponse
 * @uses   \App\Exception\MethodNotAllowedException
 * @uses   \App\Service\Response\ResponseInterface
 * @uses   \App\Exception\NotAuthenticatedException
 * @uses   \App\ValueObject\FlashMessage
 * @uses   \App\Exception\AuthenticationExceptionInterface
 */
class RouterTest extends TestCase
{
    private MockObject $userControllerMock;

    private MockObject $dashboardControllerMock;

    private MockObject $pageControllerMock;

    private MockObject $defaultControllerMock;

    private MockObject $sessionServiceMock;

    private MockObject $configMock;

    private MockObject $twigMock;

    private MockObject $routeListMock;

    private Router $router;

    public function setUp(): void
    {
        parent::setUp();

        $this->userControllerMock      = $this->createMock(UserController::class);
        $this->dashboardControllerMock = $this->createMock(DashboardController::class);
        $this->pageControllerMock      = $this->createMock(PageController::class);
        $this->defaultControllerMock   = $this->createMock(PublicController::class);
        $this->sessionServiceMock      = $this->createMock(Session::class);
        $this->configMock              = $this->createMock(Config::class);
        $this->twigMock                = $this->createMock(Environment::class);
        $this->routeListMock           = $this->createMock(RouteList::class);
        $this->router                  = new Router(
            $this->dashboardControllerMock,
            $this->userControllerMock,
            $this->pageControllerMock,
            $this->defaultControllerMock,
            $this->sessionServiceMock,
            $this->configMock,
            $this->twigMock,
            $this->routeListMock
        );
    }

    public function testRouteDynamicUri()
    {
        $content = 'Test content';
        $headers = ['http/1.1 ' . Response::STATUS_OK];

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('getHeaders')
                     ->willReturn($headers);
        $responseMock->expects($this->once())
                     ->method('getBody')
                     ->willReturn($content);

        $uriMock = $this->createMock(Uri::class);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($uriMock);

        $this->routeListMock->expects($this->once())
                            ->method('findByPath')
                            ->willReturn(null);

        $this->defaultControllerMock->expects($this->once())
                                    ->method('page')
                                    ->with($requestMock)
                                    ->willReturn($responseMock);

        $response = $this->router->route($requestMock);

        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($content, $response->getBody());
    }

    public function testRouteStaticUri()
    {
        $content = 'Test content';
        $headers = ['http/1.1 ' . Response::STATUS_OK];

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('getHeaders')
                     ->willReturn($headers);
        $responseMock->expects($this->once())
                     ->method('getBody')
                     ->willReturn($content);

        $uriMock = $this->createMock(Uri::class);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($uriMock);

        $requestMethodListMock = $this->createMock(RequestMethodList::class);
        $requestMethodListMock->expects($this->once())
                              ->method('contains')
                              ->willReturn(true);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects($this->once())
                  ->method('getController')
                  ->willReturn('dashboardController');
        $routeMock->expects($this->once())
                  ->method('getMethod')
                  ->willReturn('login');
        $routeMock->expects($this->once())
                  ->method('getAllowedRequestMethods')
                  ->willReturn($requestMethodListMock);

        $this->routeListMock->expects($this->once())
                            ->method('findByPath')
                            ->willReturn($routeMock);

        $this->dashboardControllerMock->expects($this->once())
                                      ->method('login')
                                      ->with($requestMock)
                                      ->willReturn($responseMock);

        $response = $this->router->route($requestMock);

        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($content, $response->getBody());
    }

    public function testRouteRedirectsToErrorPageOnMethodNotAllowed()
    {
        $uriMock = $this->createMock(Uri::class);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($uriMock);

        $requestMethodListMock = $this->createMock(RequestMethodList::class);
        $requestMethodListMock->expects($this->once())
                              ->method('contains')
                              ->willReturn(false);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects($this->once())
                  ->method('getAllowedRequestMethods')
                  ->willReturn($requestMethodListMock);

        $this->routeListMock->expects($this->once())
                            ->method('findByPath')
                            ->willReturn($routeMock);

        $response = $this->router->route($requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(['http/1.1 405', 'Location: /error'], $response->getHeaders());
    }

    public function testRouteRedirectsToDebugPageOnMethodNotAllowedAndDebugEnabled()
    {
        $uriMock = $this->createMock(Uri::class);

        $this->configMock->expects($this->once())
                         ->method('getByKey')
                         ->with('environment')
                         ->willReturn('DEV');

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($uriMock);

        $requestMethodListMock = $this->createMock(RequestMethodList::class);
        $requestMethodListMock->expects($this->once())
                              ->method('contains')
                              ->willReturn(false);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects($this->once())
                  ->method('getAllowedRequestMethods')
                  ->willReturn($requestMethodListMock);

        $this->routeListMock->expects($this->once())
                            ->method('findByPath')
                            ->willReturn($routeMock);

        $response = $this->router->route($requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(['http/1.1 405'], $response->getHeaders());
    }

    public function testRouteRedirectsToLoginPageOnNotAuthenticated()
    {
        $uriMock = $this->createMock(Uri::class);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($uriMock);

        $requestMethodListMock = $this->createMock(RequestMethodList::class);
        $requestMethodListMock->expects($this->once())
                              ->method('contains')
                              ->willReturn(true);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects($this->once())
                  ->method('getController')
                  ->willReturn('dashboardController');
        $routeMock->expects($this->once())
                  ->method('getMethod')
                  ->willReturn('dashboard');
        $routeMock->expects($this->once())
                  ->method('getAllowedRequestMethods')
                  ->willReturn($requestMethodListMock);
        $routeMock->expects($this->once())
                  ->method('getAllowedRequestMethods')
                  ->willReturn($requestMethodListMock);

        $this->dashboardControllerMock->expects($this->once())
                                      ->method('dashboard')
                                      ->with($requestMock)
                                      ->willThrowException(new NotAuthenticatedException());

        $this->routeListMock->expects($this->once())
                            ->method('findByPath')
                            ->willReturn($routeMock);

        $response = $this->router->route($requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(['http/1.1 302', 'Location: /admin/login'], $response->getHeaders());
    }
}