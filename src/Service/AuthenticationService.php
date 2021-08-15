<?php

namespace App\Service;

use App\Exception\ExpiredSessionException;
use App\Exception\UnknownUserException;
use App\Model\Request;
use App\Model\User\UserInterface;
use App\Repository\UserRepository;

class AuthenticationService
{
    private Config $config;

    private UserRepository $userRepository;

    private DateTimeService $dateTimeService;

    public function __construct(Config $config, UserRepository $userRepository, DateTimeService $dateTimeService)
    {
        $this->config         = $config;
        $this->userRepository = $userRepository;
        $this->dateTimeService = $dateTimeService;
    }

    public function authenticateUser(Request $request): UserInterface
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user === null) {
            throw new UnknownUserException();
        }

        if ($this->dateTimeService->now() > $user->getSessionExpiresAt()) {
            throw new ExpiredSessionException();
        }

        $this->renewSession($user);

        return $user;
    }

    public function renewSession(UserInterface $user): void
    {
        $sessionExpirationTime = $this->config->getByKey('sessionExpirationTime');
        $user->setSessionExpiresAt(($this->dateTimeService->now())->modify('+' . $sessionExpirationTime . ' minutes'));
    }
}