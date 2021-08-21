<?php

namespace App\Service;

use App\Exception\ExpiredSessionException;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Model\Request;
use App\Entity\User\UserInterface;
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
        try {
            $user = $this->userRepository->findBySessionId($request->getSessionId());
        } catch (UserNotFoundException $e) {
            throw new NotAuthenticatedException();
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