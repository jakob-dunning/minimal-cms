<?php

namespace Test;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Model\Request;
use App\Model\Response\Response;
use App\Service\Router;
use App\Service\SessionService;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRoute()
    {
        $requestUri = '/test/test';
        $content    = 'Test content';
        $headers    = ['http/1.1 ' . Response::STATUS_OK];

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('getHeaders')
                     ->willReturn($headers);
        $responseMock->expects($this->once())
                     ->method('getBody')
                     ->willReturn($content);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($requestUri);

        $userControllerMock      = $this->createMock(UserController::class);
        $dashboardControllerMock = $this->createMock(DashboardController::class);
        $pageControllerMock      = $this->createMock(PageController::class);
        $defaultControllerMock   = $this->createMock(PublicController::class);
        $sessionServiceMock      = $this->createMock(SessionService::class);

        $defaultControllerMock->expects($this->once())
                              ->method('page')
                              ->with($requestMock)
                              ->willReturn($responseMock);

        $router   = new Router(
            $dashboardControllerMock,
            $userControllerMock,
            $pageControllerMock,
            $defaultControllerMock, $sessionServiceMock
        );
        $response = $router->route($requestMock);

        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($content, $response->getBody());
    }
}