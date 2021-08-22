<?php

namespace Test\Service;

use App\Entity\User\User;
use App\Exception\ExpiredSessionException;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Service\Request;
use App\Service\Response\Response;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\Config;
use App\Service\DateTimeService;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \App\Service\AuthenticationService
 * @uses    \App\Entity\User\User
 * @uses    \App\Service\Request
 * @uses    \App\Repository\UserRepository
 * @uses    \App\Service\DateTimeService
 * @uses    \App\Exception\AuthenticationExceptionInterface
 * @uses    \App\Exception\ExpiredSessionException
 * @uses    \App\Exception\NotAuthenticatedException
 * @uses    \App\Service\Config
 */
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

    public function testAuthenticateUserThrowsNotAuthenticatedException()
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
        $this->expectExceptionCode(Response::STATUS_UNAUTHORIZED);

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

    public function testValidateNewPasswordThrowsExceptionOnMissingArrayKey() {
        $this->expectException(Exception::class);

        $this->authenticationService->validateNewPassword([]);
    }

    public function testValidateNewPasswordThrowsExceptionOnEmptyPassword() {
        $this->expectException(Exception::class);

        $this->authenticationService->validateNewPassword(['password' => '']);
    }

    public function testValidateNewPasswordThrowsExceptionOnNonMatchingPassword() {
        $this->expectException(Exception::class);

        $this->authenticationService->validateNewPassword(['password' => 'abc', 'repeat-password' => 'def']);
    }

    public function testValidateNewPasswordSucceeds() {
        $this->authenticationService->validateNewPassword(['password' => 'abc', 'repeat-password' => 'abc']);

        $this->addToAssertionCount(1);
    }
}