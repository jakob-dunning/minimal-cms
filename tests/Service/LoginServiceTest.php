<?php

namespace Test\Service;

use App\Entity\User\User;
use App\Exception\ExpiredSessionException;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Config;
use App\Service\DateTimeService;
use App\Service\LoginService;
use App\Service\PasswordService;
use App\Service\Request;
use App\Service\Response\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\LoginService
 */
class LoginServiceTest extends TestCase
{
    private LoginService $loginService;

    private MockObject $configMock;

    private MockObject $userRepositoryMock;

    private MockObject $requestMock;

    private MockObject $userMock;

    private MockObject $dateTimeServiceMock;

    private MockObject $passwordServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->configMock          = $this->createMock(Config::class);
        $this->requestMock         = $this->createMock(Request::class);
        $this->userRepositoryMock  = $this->createMock(UserRepository::class);
        $this->userMock            = $this->createMock(User::class);
        $this->dateTimeServiceMock = $this->createMock(DateTimeService::class);
        $this->passwordServiceMock = $this->createMock(PasswordService::class);

        $this->loginService = new LoginService(
            $this->passwordServiceMock,
            $this->userRepositoryMock,
            $this->dateTimeServiceMock,
            $this->configMock
        );
    }

    public function testLoginThrowsNotAuthenticatedException()
    {
        $sessionId = 'hjsdfkglsdgflisdhfui34563456';

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findBySessionId')
                                 ->with($sessionId)
                                 ->willThrowException(new UserNotFoundException());

        $this->requestMock->expects($this->once())
                          ->method('getSessionId')
                          ->willReturn($sessionId);

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage(NotAuthenticatedException::MESSAGE);
        $this->expectExceptionCode(Response::STATUS_UNAUTHORIZED);

        $this->loginService->login($this->requestMock);
    }

    public function testLoginThrowsExpiredSessionException()
    {
        $sessionId = 'hjsdfkglsdgflisdhfui34563456';

        $this->userMock->expects($this->once())
                       ->method('getSessionExpiresAt')
                       ->willReturn((new \DateTime())->modify('-12 minutes '));

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findBySessionId')
                                 ->with($sessionId)
                                 ->willReturn($this->userMock);

        $this->requestMock->expects($this->once())
                          ->method('getSessionId')
                          ->willReturn($sessionId);

        $this->dateTimeServiceMock->expects($this->once())
                                  ->method('now')
                                  ->willReturn(new \DateTime());

        $this->expectException(ExpiredSessionException::class);
        $this->expectExceptionMessage(ExpiredSessionException::MESSAGE);
        $this->expectExceptionCode(Response::STATUS_UNAUTHORIZED);

        $this->loginService->login($this->requestMock);
    }

    public function testLogin()
    {
        $sessionId             = 'hjsdfkglsdgflissaddhfui34563456';
        $username              = 'Bartleby';
        $sessionExpirationTime = 10;

        $this->userMock->expects($this->exactly(2))
                       ->method('getSessionExpiresAt')
                       ->willReturn((new \DateTime())->modify("+{$sessionExpirationTime} minutes"));
        $this->userMock->expects($this->once())
                       ->method('getUsername')
                       ->willReturn($username);
        $this->userMock->expects($this->once())
                       ->method('getSessionId')
                       ->willReturn($sessionId);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findBySessionId')
                                 ->with($sessionId)
                                 ->willReturn($this->userMock);

        $this->dateTimeServiceMock->expects($this->exactly(2))
                                  ->method('now')
                                  ->willReturn(new \DateTime());

        $this->configMock->expects($this->once())
                         ->method('getByKey')
                         ->with('sessionExpirationTime')
                         ->willReturn($sessionExpirationTime);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getSessionId')
                    ->willReturn($sessionId);

        $user = $this->loginService->login($requestMock);

        $this->assertSame('Bartleby', $user->getUserName());
        $this->assertSame($sessionId, $user->getSessionId());
        $this->assertSame(
            (new \DateTime())->modify("+ {$sessionExpirationTime} minutes")->format(DateTimeService::FORMAT_SQL),
            $user->getSessionExpiresAt()->format(DateTimeService::FORMAT_SQL)
        );
    }

    public function testRenewSession()
    {
        $sessionExpirationTime = 45;
        $now                   = new \DateTime();

        $this->dateTimeServiceMock->expects($this->once())
                                  ->method('now')
                                  ->willReturn($now);

        $this->configMock->expects($this->once())
                         ->method('getByKey')
                         ->with('sessionExpirationTime')
                         ->willReturn($sessionExpirationTime);

        $this->userMock->expects($this->once())
                       ->method('setSessionExpiresAt')
                       ->with($now->modify("+{$sessionExpirationTime} minutes"));

        $this->loginService->renewSession($this->userMock);
    }
}