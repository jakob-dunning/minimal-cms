<?php

namespace App\Controller\Admin;

use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use App\Service\SessionService;
use App\ValueObject\FlashMessage;
use App\ValueObject\Uri;
use Twig\Environment;

class UserController
{
    private UserRepository $userRepository;

    private Environment $twig;

    private AuthenticationService $authenticationService;

    private SessionService $sessionService;

    public function __construct(
        UserRepository $userRepository,
        Environment $twig,
        AuthenticationService $authenticationService,
        SessionService $sessionService
    ) {
        $this->userRepository        = $userRepository;
        $this->twig                  = $twig;
        $this->authenticationService = $authenticationService;
        $this->sessionService        = $sessionService;
    }

    public function create(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response($this->twig->render('user/single.html.twig', ['title' => 'Add User']));
        }

        $post = $request->post();

        if ($post['password'] === '') {
            $this->sessionService->addFlash(FlashMessage::createFromParameters('Password cannot be empty', FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString('/admin/user/create'));
        }

        if ($post['password'] !== $post['repeat-password']) {
            $this->sessionService->addFlash(FlashMessage::createFromParameters('Passwords do not match', FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString('/admin/user/create'));
        }

        $password = password_hash($post['password'], PASSWORD_DEFAULT);

        try {
            $this->userRepository->create($post['user'], $password);
        } catch (\Throwable $t) {
            $this->sessionService->addFlash(FlashMessage::createFromParameters($t->getMessage(), FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString('/admin/user/create'));
        }

        $this->sessionService->addFlash(FlashMessage::createFromParameters('User created successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/user'));
    }

    public function list(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $users = $this->userRepository->findAll();

        return new Response(
            $this->twig->render('user/list.html.twig', ['users' => $users])
        );
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $get = $request->get();
        $user = $this->userRepository->findById($get['id']);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response(
                $this->twig->render('user/single.html.twig', ['title' => 'Edit user', 'user' => $user, 'formTarget'=> "/admin/user/edit?id={$user->getId()}"])
            );
        }

        $post = $request->post();

        if ($post['password'] === '') {
            $this->sessionService->addFlash(FlashMessage::createFromParameters('Password cannot be empty', FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString("/admin/user/edit?id={$user->getId()}"));
        }

        if ($post['password'] !== $post['repeat-password']) {
            $this->sessionService->addFlash(FlashMessage::createFromParameters('Passwords do not match', FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString("/admin/user/edit?id={$user->getId()}"));
        }

        $user->setPassword(password_hash($post['password'], PASSWORD_DEFAULT));
        $this->userRepository->persist($user);
        $this->sessionService->addFlash(FlashMessage::createFromParameters('New password saved successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/user'));
    }

    public function delete(Request $request): ResponseInterface
    {
        $this->authenticationService->authenticateUser($request);

        $get = $request->get();
        $this->userRepository->deleteById($get['id']);
        $this->sessionService->addFlash(FlashMessage::createFromParameters('User deleted successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/user'));
    }
}