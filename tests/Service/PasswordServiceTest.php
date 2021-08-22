<?php

namespace Test\Service;

use App\Repository\UserRepository;
use App\Service\Config;
use App\Service\DateTimeService;
use App\Service\PasswordService;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \App\Service\PasswordService
 * @uses    \App\Entity\User\User
 * @uses    \App\Service\Request
 * @uses    \App\Repository\UserRepository
 * @uses    \App\Service\DateTimeService
 * @uses    \App\Exception\AuthenticationExceptionInterface
 * @uses    \App\Exception\ExpiredSessionException
 * @uses    \App\Exception\NotAuthenticatedException
 * @uses    \App\Service\Config
 */
class PasswordServiceTest extends TestCase
{
    private PasswordService $passwordService;

    private MockObject $configMock;

    private MockObject $userRepositoryMock;

    private MockObject $dateTimeServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->configMock          = $this->createMock(Config::class);
        $this->userRepositoryMock  = $this->createMock(UserRepository::class);
        $this->dateTimeServiceMock = $this->createMock(DateTimeService::class);

        $this->passwordService = new PasswordService($this->configMock, $this->userRepositoryMock, $this->dateTimeServiceMock);
    }

    public function testValidateNewPasswordThrowsExceptionOnMissingArrayKey()
    {
        $this->expectException(Exception::class);

        $this->passwordService->validateNewPassword([]);
    }

    public function testValidateNewPasswordThrowsExceptionOnEmptyPassword()
    {
        $this->expectException(Exception::class);

        $this->passwordService->validateNewPassword(['password' => '']);
    }

    public function testValidateNewPasswordThrowsExceptionOnNonMatchingPassword()
    {
        $this->expectException(Exception::class);

        $this->passwordService->validateNewPassword(['password' => 'abc', 'repeat-password' => 'def']);
    }

    public function testValidateNewPasswordSucceeds()
    {
        $this->passwordService->validateNewPassword(['password' => 'abc', 'repeat-password' => 'abc']);

        $this->addToAssertionCount(1);
    }
}