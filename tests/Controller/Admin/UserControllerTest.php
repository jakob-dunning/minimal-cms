<?php

use App\Controller\Admin\UserController;
use App\Entity\User\User;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\Request;
use App\Service\Response\RedirectResponse;
use App\Service\Response\Response;
use App\Service\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

/**
 * @covers \App\Controller\Admin\UserController
 * @uses   \App\Service\Response\Response
 * @uses   \App\Service\Response\RedirectResponse
 * @uses   \App\ValueObject\FlashMessage
 * @uses   \App\ValueObject\Uri
 */
class UserControllerTest extends TestCase
{
    private UserController $userController;

    private MockObject $sessionServiceMock;

    private MockObject $authenticationServiceMock;

    private MockObject $twigMock;

    private MockObject $userRepositoryMock;

    private MockObject $requestMock;

    public function setUp(): void
    {
        $this->userRepositoryMock        = $this->createMock(UserRepository::class);
        $this->twigMock                  = $this->createMock(Environment::class);
        $this->authenticationServiceMock = $this->createMock(AuthenticationService::class);
        $this->sessionServiceMock        = $this->createMock(Session::class);
        $this->requestMock               = $this->createMock(Request::class);

        $this->userController = new UserController(
            $this->userRepositoryMock,
            $this->twigMock,
            $this->authenticationServiceMock,
            $this->sessionServiceMock
        );
    }

    public function testViewCreateForm()
    {
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_GET);

        $response = $this->userController->create($this->requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::STATUS_OK, $response->getStatusCode());
    }

    public function testCreateFormRedirectsOnInvalidPassword()
    {
        $post = [];
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('validateNewPassword')
                                        ->with($post)
                                        ->willThrowException(new \Exception());

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $response = $this->userController->create($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/user/create"], $response->getHeaders());
    }

    public function testCreateFormRedirectsOnRepositoryError()
    {
        $post           = ['user' => 'heini', 'password' => 'abc', 'repeat-password' => 'abc'];
        $hashedPassword = '$2y$10$A6GORwPx2KW3zmLe1EyfOuDrvOzF.bF3NtNdvSf7QM6diD3pbQcqu';

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('validateNewPassword')
                                        ->with($post);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('hashPassword')
                                        ->with($post['password'])
                                        ->willReturn($hashedPassword);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('create')
                                 ->with($post['user'], $hashedPassword)
                                 ->willThrowException(new \Exception());

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $response = $this->userController->create($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/user/create"], $response->getHeaders());
    }

    public function testCreateFormRedirectsOnSuccess()
    {
        $post           = ['user' => 'heini', 'password' => 'abc', 'repeat-password' => 'abc'];
        $hashedPassword = '$2y$10$A6GORwPx2KW3zmLe1EyfOuDrvOzF.bF3NtNdvSf7QM6diD3pbQcqu';

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('validateNewPassword')
                                        ->with($post);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('hashPassword')
                                        ->with($post['password'])
                                        ->willReturn($hashedPassword);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('create')
                                 ->with($post['user'], $hashedPassword);

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $response = $this->userController->create($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/user"], $response->getHeaders());
    }

    public function testViewList()
    {
        $this->userRepositoryMock->expects($this->once())
                                 ->method('findAll');

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);

        $response = $this->userController->list($this->requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(["http/1.1 200"], $response->getHeaders());
    }

    public function testViewEditForm()
    {
        $userId = 9;

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_GET);
        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn(['id' => $userId]);

        $userMock = $this->createMock(User::class);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findById')
                                 ->with($userId)
                                 ->willReturn($userMock);

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);

        $response = $this->userController->edit($this->requestMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(["http/1.1 200"], $response->getHeaders());
    }

    public function testEditFormRedirectsOnInvalidPassword()
    {
        $userId = 9;
        $post   = ['idasd' => 'kahusdiuh'];

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn(['id' => $userId]);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('getId')
                 ->willReturn($userId);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findById')
                                 ->with($userId)
                                 ->willReturn($userMock);

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('validateNewPassword')
                                        ->with($post)
                                        ->willThrowException(new \Exception());

        $response = $this->userController->edit($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/user/edit?id={$userId}"], $response->getHeaders());
    }

    public function testEditFormRedirectsOnSuccess()
    {
        $userId         = 9;
        $post           = ['idasd' => 'kahusdiuh', 'password' => 'sdfsdfsdf'];
        $hashedPassword = '$2y$10$A6GORwPx2KW3zmLe1EyfOuDrvOzF.bF3NtNdvSf7QM6diD3pbQcqu';

        $this->requestMock->expects($this->once())
                          ->method('getMethod')
                          ->willReturn(Request::METHOD_POST);
        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn(['id' => $userId]);
        $this->requestMock->expects($this->once())
                          ->method('post')
                          ->willReturn($post);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('setPassword')
                 ->with($hashedPassword);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findById')
                                 ->with($userId)
                                 ->willReturn($userMock);
        $this->userRepositoryMock->expects($this->once())
                                 ->method('persist')
                                 ->with($userMock);

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('validateNewPassword')
                                        ->with($post);
        $this->authenticationServiceMock->expects($this->once())
                                        ->method('hashPassword')
                                        ->with($post['password'])
                                        ->willReturn($hashedPassword);

        $response = $this->userController->edit($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/user"], $response->getHeaders());
    }

    public function testDelete()
    {
        $userId = 9;
        $get    = ['id' => $userId];

        $this->authenticationServiceMock->expects($this->once())
                                        ->method('authenticateUser')
                                        ->with($this->requestMock);

        $this->requestMock->expects($this->once())
                          ->method('get')
                          ->willReturn($get);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('deleteById')
                                 ->with($userId);

        $response = $this->userController->delete($this->requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(["http/1.1 302", "Location: /admin/user"], $response->getHeaders());
    }
}