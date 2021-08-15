<?php

namespace Test;

use App\Exception\ExpiredSessionException;
use App\Exception\UnknownUserException;
use App\Model\Request;
use App\Entity\User\User;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\Config;
use App\Service\DateTimeService;
use PHPUnit\Framework\TestCase;

class AuthenticationServiceTest extends TestCase
{
    private $authenticationService;

    private $configMock;

    private $userRepositoryMock;

    private $requestMock;

    private $userMock;

    private $dateTimeServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->configMock            = $this->createMock(Config::class);
        $this->requestMock           = $this->createMock(Request::class);
        $this->userRepositoryMock    = $this->createMock(UserRepository::class);
        $this->userMock              = $this->createMock(User::class);
        $this->dateTimeServiceMock   = $this->createMock(DateTimeService::class);
        $this->authenticationService = new AuthenticationService($this->configMock, $this->userRepositoryMock, $this->dateTimeServiceMock);
    }

    public function testAuthenticateUserThrowsUnknownUserException()
    {
        $sessionId = 'hjsdfkglsdgflisdhfui34563456';

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findBySessionId')
                                 ->with($sessionId)
                                 ->willReturn(null);

        $this->requestMock->expects($this->once())
                          ->method('getSessionId')
                          ->willReturn($sessionId);

        $this->expectException(UnknownUserException::class);
        $this->expectExceptionMessage(UnknownUserException::MESSAGE);
        $this->expectExceptionCode(401);

        $this->authenticationService->authenticateUser($this->requestMock);
    }

    public function testAuthenticateUserThrowsExpiredSessionException()
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
        $this->expectExceptionCode(401);

        $this->authenticationService->authenticateUser($this->requestMock);
    }

    public function testAuthenticateUser()
    {
        $sessionId = 'hjsdfkglsdgflissaddhfui34563456';
        $username  = 'Bartleby';

        $this->configMock->expects($this->once())
                         ->method('getByKey')
                         ->with('sessionExpirationTime')
                         ->willReturn(55);

        $this->userMock->expects($this->once())
                       ->method('getSessionExpiresAt')
                       ->willReturn((new \DateTime())->modify('+55 minutes'));
        $this->userMock->expects($this->once())
                       ->method('getUsername')
                       ->willReturn($username);

        $this->userRepositoryMock->expects($this->once())
                                 ->method('findBySessionId')
                                 ->with($sessionId)
                                 ->willReturn($this->userMock);

        $this->dateTimeServiceMock->expects($this->exactly(2))
                                  ->method('now')
                                  ->willReturn(new \DateTime());

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
                    ->method('getSessionId')
                    ->willReturn($sessionId);

        $user = $this->authenticationService->authenticateUser($requestMock);

        $this->assertSame('Bartleby', $user->getUserName());
    }

    public function testRenewSession()
    {
        $sessionExpirationTime = 45;

        $now = new \DateTime();
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

        $this->authenticationService->renewSession($this->userMock);
    }
}