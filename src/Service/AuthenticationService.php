<?php

namespace App\Service;

use App\Entity\User\UserInterface;
use App\Exception\ExpiredSessionException;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use function key_exists;
use function password_hash;
use const PASSWORD_DEFAULT;

class AuthenticationService
{
    private Config $config;

    private UserRepository $userRepository;

    private DateTimeService $dateTimeService;

    public function __construct(Config $config, UserRepository $userRepository, DateTimeService $dateTimeService)
    {
        $this->config          = $config;
        $this->userRepository  = $userRepository;
        $this->dateTimeService = $dateTimeService;
    }

    public function loginUser(Request $request): UserInterface
    {
        try {
            $user = $this->userRepository->findBySessionId($request->getSessionId());
        } catch (UserNotFoundException $e) {
            throw new NotAuthenticatedException();
        }

        if ($this->dateTimeService->now() > $user->getSessionExpiresAt()) {
            throw new ExpiredSessionException();
        }

        $this->renewSession($user, $request->getSessionId());

        return $user;
    }

    public function renewSession(UserInterface $user, string $sessionId): void
    {
        if ($user->getSessionId() === null) {
            $user->setSessionId($sessionId);
        }
        $sessionExpirationTime = $this->config->getByKey('sessionExpirationTime');
        $user->setSessionExpiresAt(($this->dateTimeService->now())->modify('+' . $sessionExpirationTime . ' minutes'));
        $this->userRepository->persist($user);
    }

    /**
     * @codeCoverageIgnore
     */
    public function verifyPassword(string $actual, string $expected): bool
    {
        return password_verify($actual, $expected);
    }

    public function validateNewPassword(array $post): void
    {
        if (key_exists('password', $post) === false) {
            throw new \Exception('Password cannot be empty');
        }

        if ($post['password'] === '') {
            throw new \Exception('Password cannot be empty');
        }

        if ($post['password'] !== $post['repeat-password']) {
            throw new \Exception('Passwords do not match');
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}