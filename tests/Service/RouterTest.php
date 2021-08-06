<?php

namespace Test;

use App\Model\Page;
use App\Model\Request;
use App\Model\Response;
use App\Service\Database;
use App\Service\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRoute()
    {
        $requestUri = '/test/test';
        $content    = 'Test content';

        $pageMock = $this->createMock(Page::class);
        $pageMock->expects($this->once())
                 ->method('getContent')
                 ->willReturn($content);

        $databaseMock = $this->createMock(Database::class);
        $databaseMock->expects($this->once())
                     ->method('findPageByUri')
                     ->with($requestUri)
                     ->willReturn($pageMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getUri')
                    ->willReturn($requestUri);

        $router   = new Router($databaseMock);
        $response = $router->route($requestMock);

        $this->assertSame(['http/1.1 ' . Response::STATUS_OK], $response->getHeaders());
        $this->assertSame($content, $response->getBody());
    }
}