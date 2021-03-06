<?php

use App\Controller\Admin\PageController;
use App\Entity\Page;
use App\Repository\PageRepository;
use App\Service\PasswordService;
use App\Service\LoginService;
use App\Service\Request;
use App\Service\Response\RedirectResponse;
use App\Service\Response\Response;
use App\Service\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

/**
 * @covers \App\Controller\Admin\PageController
 * @uses   \App\Service\Response\Response
 * @uses   \App\ValueObject\Uri
 * @uses   \App\ValueObject\FlashMessage
 * @uses   \App\Service\Response\RedirectResponse
 */
class PageControllerTest extends TestCase
{
    private MockObject $pageRepositoryMock;

    private MockObject $twigMock;

    private MockObject $sessionServiceMock;

    private PageController $pageController;

    private MockObject $requestMock;

    private LoginService $loginServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->pageRepositoryMock        = $this->createMock(PageRepository::class);
        $this->twigMock                  = $this->createMock(Environment::class);
        $this->sessionServiceMock        = $this->createMock(Session::class);
        $this->requestMock               = $this->createMock(Request::class);
        $this->loginServiceMock          = $this->createMock(LoginService::class);

        $this->pageController = new PageController(
            $this->pageRepositoryMock,
            $this->twigMock,
            $this->sessionServiceMock,
            $this->loginServiceMock
        );
    }

    public function testViewList()
    {
        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $this->pageRepositoryMock->expects($this->once())
                                 ->method('findAll')
                                 ->willReturn([]);

        $response = $this->pageController->list($this->requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::STATUS_OK, $response->getStatusCode());
    }

    public function testViewCreateForm()
    {
        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_GET);

        $response = $this->pageController->create($this->requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::STATUS_OK, $response->getStatusCode());
    }

    public function testCreateFormRedirectsOnRepositoryError()
    {
        $post = ['uri' => '/asdfasd', 'title' => 'asdasd', 'content' => 'ksdfhuih789zs78df'];

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $this->pageRepositoryMock->expects($this->once())
                                 ->method('create')
                                 ->with($post['uri'], $post['title'], $post['content'])
                                 ->willThrowException(new \Exception());

        $response = $this->pageController->create($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/page/create"], $response->getHeaders());
    }

    public function testCreateFormRedirectsOnSuccess()
    {
        $post = ['uri' => '/asdfasd', 'title' => 'asdasd', 'content' => 'ksdfhuih789zs78df'];

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $this->pageRepositoryMock->expects($this->once())
                                 ->method('create')
                                 ->with($post['uri'], $post['title'], $post['content']);

        $response = $this->pageController->create($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/page"], $response->getHeaders());
    }

    public function testViewEditForm()
    {
        $get = ['id' => 7];

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_GET);
        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn($get);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $pageMock = $this->createMock(Page::class);

        $this->pageRepositoryMock->expects($this->once())
                                 ->method('findById')
                                 ->with($get['id'])
                                 ->willReturn($pageMock);

        $response = $this->pageController->edit($this->requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(["http/1.1 200"], $response->getHeaders());
    }

    public function testEditFormRedirectsOnSuccess()
    {
        $get  = ['id' => 7];
        $post = ['content' => 'asdasd', 'title' => 'sdfsfdsdf', 'uri' => '/345g'];

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn($get);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $pageMock = $this->createMock(Page::class);
        $pageMock->expects($this->once())
                 ->method('setContent')
                 ->with($post['content'])
                 ->willReturn($pageMock);
        $pageMock->expects($this->once())
                 ->method('setTitle')
                 ->with($post['title'])
                 ->willReturn($pageMock);
        $pageMock->expects($this->once())
                 ->method('setUri')
                 ->with($post['uri'])
                 ->willReturn($pageMock);

        $this->pageRepositoryMock->expects($this->once())
                                 ->method('findById')
                                 ->with($get['id'])
                                 ->willReturn($pageMock);
        $this->pageRepositoryMock->expects($this->once())
                                 ->method('persist')
                                 ->with($pageMock);

        $response = $this->pageController->edit($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/page"], $response->getHeaders());
    }

    public function testDeleteRedirectsOnSuccess()
    {
        $get = ['id' => 7];

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($this->requestMock);

        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn($get);

        $this->pageRepositoryMock->expects($this->once())
                                 ->method('deleteById')
                                 ->with($get['id']);

        $response = $this->pageController->delete($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/page"], $response->getHeaders());
    }
}