<?php

namespace App\Service;

use App\Exception\AnonymousUserException;
use App\Model\Request;
use App\Model\User\UserInterface;
use App\Repository\UserRepository;

class Authentication
{
    private Config $config;

    private UserRepository $userRepository;

    public function __construct(Config $config, UserRepository $userRepository)
    {
        $this->config         = $config;
        $this->userRepository = $userRepository;
    }

    public function authenticateUser(Request $request): UserInterface
    {
        $user = $this->userRepository->findBySessionId($request->getSessionId());

        if ($user->isAuthenticated() === false) {
            throw new AnonymousUserException();
        }

        $this->renewSession($user);

        return $user;
    }

    public function renewSession(UserInterface $user): void
    {
        $sessionExpirationTime = $this->config->getByKey('sessionExpirationTime');
        $user->setSessionIdExpiresAt((new \DateTime())->modify('+' . $sessionExpirationTime . ' minutes'));
    }
}