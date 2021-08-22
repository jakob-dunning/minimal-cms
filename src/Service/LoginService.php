<?php

namespace App\Service;

use App\Entity\User\UserInterface;
use App\Exception\ExpiredSessionException;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;

class LoginService
{
    private PasswordService $passwordService;

    private UserRepository $userRepository;

    private DateTimeService $dateTimeService;

    private Config $config;

    public function __construct(
        PasswordService $passwordService,
        UserRepository $userRepository,
        DateTimeService $dateTimeService,
        Config $config
    ) {
        $this->passwordService = $passwordService;
        $this->userRepository        = $userRepository;
        $this->dateTimeService       = $dateTimeService;
        $this->config                = $config;
    }

    public function login(Request $request): UserInterface
    {
        $user = $this->findAuthenticatedUser($request);

        $this->renewSession($user);
        $this->userRepository->persist($user);

        return $user;
    }

    private function findAuthenticatedUser(Request $request): UserInterface
    {
        try {
            $user = $this->userRepository->findBySessionId($request->getSessionId());
        } catch (UserNotFoundException $e) {
            throw new NotAuthenticatedException();
        }

        if ($this->dateTimeService->now() > $user->getSessionExpiresAt()) {
            throw new ExpiredSessionException();
        }

        return $user;
    }

    public function renewSession(UserInterface $user): void
    {
        $sessionExpirationTime = $this->config->getByKey('sessionExpirationTime');
        $user->setSessionExpiresAt(($this->dateTimeService->now())->modify('+' . $sessionExpirationTime . ' minutes'));
    }
}