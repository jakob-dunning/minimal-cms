<?php

namespace App\Service;

use App\Entity\User\UserInterface;
use App\Repository\UserRepository;

class LoginService
{
    private AuthenticationService $authenticationService;

    private UserRepository $userRepository;

    public function __construct(AuthenticationService $authenticationService, UserRepository $userRepository)
    {
        $this->authenticationService = $authenticationService;
        $this->userRepository        = $userRepository;
    }

    public function login(Request $request): UserInterface
    {
        $user = $this->authenticationService->findAuthenticatedUser($request);

        $this->authenticationService->renewSession($user);
        $this->userRepository->persist($user);

        return $user;
    }
}