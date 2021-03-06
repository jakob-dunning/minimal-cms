<?php

use App\Controller\Admin\DashboardController;
use App\Entity\User\User;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\Config;
use App\Service\LoginService;
use App\Service\PasswordService;
use App\Service\Request;
use App\Service\Response\RedirectResponse;
use App\Service\Response\Response;
use App\Service\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

/**
 * @covers \App\Controller\Admin\DashboardController
 * @uses   \App\Service\Response\Response
 * @uses   \App\Repository\PageRepository
 * @uses   \App\Repository\UserRepository
 * @uses   \App\Service\PasswordService
 * @uses   \App\Service\Config
 * @uses   \App\Service\Response\RedirectResponse
 * @uses   \App\Service\Response\ResponseInterface
 * @uses   \App\Service\Session
 * @uses   \App\ValueObject\Uri
 * @uses   \App\Exception\AuthenticationExceptionInterface
 * @uses   \App\Service\LoginService
 */
class DashboardControllerTest extends TestCase
{
    private DashboardController $dashboardController;

    private MockObject          $pageRepositoryMock;

    private MockObject          $configMock;

    private MockObject          $twigMock;

    private MockObject          $passwordServiceMock;

    private MockObject          $userRepositoryMock;

    private MockObject          $sessionServiceMock;

    private MockObject          $loginServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock  = $this->createMock(UserRepository::class);
        $this->pageRepositoryMock  = $this->createMock(PageRepository::class);
        $this->configMock          = $this->createMock(Config::class);
        $this->twigMock            = $this->createMock(Environment::class);
        $this->passwordServiceMock = $this->createMock(PasswordService::class);
        $this->sessionServiceMock  = $this->createMock(Session::class);
        $this->loginServiceMock    = $this->createMock(LoginService::class);

        $this->dashboardController = new DashboardController(
            $this->userRepositoryMock,
            $this->pageRepositoryMock,
            $this->configMock,
            $this->twigMock,
            $this->passwordServiceMock,
            $this->sessionServiceMock,
            $this->loginServiceMock
        );
    }

    public function testViewLoginScreen()
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getMethod')
                    ->willReturn(Request::METHOD_GET);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($requestMock)
                               ->willThrowException(new NotAuthenticatedException());

        $response = $this->dashboardController->login($requestMock);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testAuthenticatedUserIsRedirectedToDashboard()
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getMethod')
                    ->willReturn(Request::METHOD_GET);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($requestMock);

        $response = $this->dashboardController->login($requestMock);

        $this->assertSame(["http/1.1 302", "Location: /admin/dashboard"], $response->getHeaders());
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testUserCanLogin()
    {
        $username         = 'hanni';
        $expectedPassword = 'hansni';
        $actualPassword   = 'hansni';

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getMethod')
                    ->willReturn(Request::METHOD_POST);
        $requestMock->expects($this->exactly(2))
                    ->method('post')
                    ->willReturn(['user' => $username, 'password' => $actualPassword]);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('getPassword')
                 ->willReturn($expectedPassword);

        $this->loginServiceMock->expects($this->once())
                               ->method('renewSession')
                               ->with($userMock);
        $this->passwordServiceMock->expects($this->once())
                                  ->method('verifyPassword')
                                  ->with($expectedPassword, $actualPassword)
                                  ->willReturn(true);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findByUsername')
                                 ->with($username)
                                 ->willReturn($userMock);

        $response = $this->dashboardController->login($requestMock);

        $this->assertSame(["http/1.1 302", "Location: /admin/dashboard"], $response->getHeaders());
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testLoginThrowsExceptionOnUserNotFound()
    {
        $username       = 'hanni';
        $actualPassword = 'hansni';

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getMethod')
                    ->willReturn(Request::METHOD_POST);
        $requestMock->expects($this->once())
                    ->method('post')
                    ->willReturn(['user' => $username, 'password' => $actualPassword]);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findByUsername')
                                 ->willThrowException(new UserNotFoundException());

        $this->expectException(NotAuthenticatedException::class);

        $this->dashboardController->login($requestMock);
    }

    public function testLoginThrowsExceptionOnBadPassword()
    {
        $username         = 'hanni';
        $actualPassword   = 'hansni';
        $expectedPassword = '34562356';

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getMethod')
                    ->willReturn(Request::METHOD_POST);
        $requestMock->expects($this->exactly(2))
                    ->method('post')
                    ->willReturn(['user' => $username, 'password' => $actualPassword]);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('getPassword')
                 ->willReturn($expectedPassword);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findByUsername')
                                 ->willReturn($userMock);

        $this->passwordServiceMock->expects($this->once())
                                  ->method('verifyPassword')
                                  ->with($actualPassword, $expectedPassword)
                                  ->willReturn(false);

        $this->expectException(NotAuthenticatedException::class);

        $this->dashboardController->login($requestMock);
    }

    public function testUserCanLogout()
    {
        $requestMock = $this->createMock(Request::class);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('setSessionExpiresAt')
                 ->with(null)
                 ->willReturn($userMock);
        $userMock->expects($this->once())
                 ->method('setSessionId')
                 ->with(null)
                 ->willReturn($userMock);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($requestMock)
                               ->willReturn($userMock);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('persist')
                                 ->with($userMock);

        $response = $this->dashboardController->logout($requestMock);

        $this->assertSame(["http/1.1 302", "Location: /admin/login"], $response->getHeaders());
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testUserCanViewDashboard()
    {
        $requestMock = $this->createMock(Request::class);

        $this->loginServiceMock->expects($this->once())
                               ->method('login')
                               ->with($requestMock);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findAll');
        $this->userRepositoryMock->expects($this->once())
                                 ->method('findAll');

        $this->twigMock->expects($this->once())
                       ->method('render');

        $response = $this->dashboardController->dashboard($requestMock);

        $this->assertSame(["http/1.1 200"], $response->getHeaders());
        $this->assertInstanceOf(Response::class, $response);
    }
}