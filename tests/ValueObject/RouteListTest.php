<?php

use App\Model\Request;
use App\ValueObject\RequestMethodList;
use App\ValueObject\Route;
use App\ValueObject\RouteList;
use App\ValueObject\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\RouteList
 * @uses   \App\ValueObject\RequestMethodList
 * @uses   \App\ValueObject\Uri
 * @uses   \App\ValueObject\Route
 * @uses   \App\Model\Request
 */
class RouteListTest extends TestCase
{
    public function testCreateFromArray()
    {
        $path          = '/agjvsdjgsadjk';
        $controller    = 'hansenController';
        $method        = 'hansenMethode';
        $requestMethod = Request::METHOD_GET;

        $path2          = 'kuashdhohjoi';
        $controller2    = 'hansenController';
        $method2        = 'hansenMethode';
        $requestMethod2 = Request::METHOD_POST;

        $routeList = RouteList::createFromArray(
            [
                ['uri' => $path, 'controller' => $controller, 'method' => $method, 'allowedRequestMethods' => [$requestMethod]],
                ['uri' => $path2, 'controller' => $controller2, 'method' => $method2, 'allowedRequestMethods' => [$requestMethod2]],
            ]
        );

        /** @var Route $route */
        $route = $routeList->all()[0];
        /** @var Route $route2 */
        $route2 = $routeList->all()[1];

        $this->assertCount(2, $routeList->all());

        $this->assertSame($path, $route->getUri()->getPath());
        $this->assertSame($controller, $route->getController());
        $this->assertSame($method, $route->getMethod());
        $this->assertTrue($route->getAllowedRequestMethods()->contains($requestMethod));

        $this->assertSame($path2, $route2->getUri()->getPath());
        $this->assertSame($controller2, $route2->getController());
        $this->assertSame($method2, $route2->getMethod());
        $this->assertTrue($route2->getAllowedRequestMethods()->contains($requestMethod2));
    }

    public function testAdd()
    {
        $uriMock               = $this->createMock(Uri::class);
        $controller            = 'hansenController';
        $method                = 'hansenMethode';
        $requestMethodListMock = $this->createMock(RequestMethodList::class);

        $uriMock2               = $this->createMock(Uri::class);
        $controller2            = 'hansenController';
        $method2                = 'hansenMethode';
        $requestMethodListMock2 = $this->createMock(RequestMethodList::class);

        $routeList = RouteList::createFromArray([]);

        $routeList->add(new Route($uriMock, $controller, $method, $requestMethodListMock));
        $routeList->add(new Route($uriMock2, $controller2, $method2, $requestMethodListMock2));

        /** @var Route $route */
        $route = $routeList->all()[0];
        /** @var Route $route */
        $route2 = $routeList->all()[1];

        $this->assertCount(2, $routeList->all());

        $this->assertSame($uriMock, $route->getUri());
        $this->assertSame($controller, $route->getController());
        $this->assertSame($method, $route->getMethod());
        $this->assertSame($requestMethodListMock, $route->getAllowedRequestMethods());

        $this->assertSame($uriMock2, $route2->getUri());
        $this->assertSame($controller2, $route2->getController());
        $this->assertSame($method2, $route2->getMethod());
        $this->assertSame($requestMethodListMock2, $route2->getAllowedRequestMethods());
    }

    public function testFindByPathReturnsPath()
    {
        $path    = 'ilasjfe';
        $uriMock = $this->createMock(Uri::class);
        $uriMock->expects($this->once())
                ->method('getPath')
                ->willReturn($path);
        $controller            = 'hansenController';
        $method                = 'hansenMethode';
        $requestMethodListMock = $this->createMock(RequestMethodList::class);

        $routeList = RouteList::createFromArray([]);
        $routeList->add(new Route($uriMock, $controller, $method, $requestMethodListMock));

        /** @var Route $route */
        $route = $routeList->findByPath($path);

        $this->assertSame($uriMock, $route->getUri());
        $this->assertSame($controller, $route->getController());
        $this->assertSame($method, $route->getMethod());
        $this->assertSame($requestMethodListMock, $route->getAllowedRequestMethods());
    }

    public function testFindByPathReturnsNull()
    {
        $routeList = RouteList::createFromArray([]);

        $route = $routeList->findByPath('/agsdfugaduz');

        $this->assertSame(null, $route);
    }
}