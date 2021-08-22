<?php

namespace App\Controller\Admin;

use App\Service\LoginService;
use App\Service\Request;
use App\Service\Response\RedirectResponse;
use App\Service\Response\Response;
use App\Service\Response\ResponseInterface;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use App\Service\Session;
use App\ValueObject\FlashMessage;
use App\ValueObject\Uri;
use Twig\Environment;

class UserController
{
    private UserRepository $userRepository;

    private Environment $twig;

    private PasswordService $passwordService;

    private Session $sessionService;

    private LoginService $loginService;

    public function __construct(
        UserRepository $userRepository,
        Environment $twig,
        PasswordService $passwordService,
        Session $sessionService,
        LoginService $loginService
    ) {
        $this->userRepository        = $userRepository;
        $this->twig                  = $twig;
        $this->passwordService = $passwordService;
        $this->sessionService        = $sessionService;
        $this->loginService = $loginService;
    }

    public function create(Request $request): ResponseInterface
    {
        $this->loginService->login($request);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response($this->twig->render('user/single.html.twig', ['title' => 'Add User']));
        }

        $post = $request->post();

        try {
            $this->passwordService->validateNewPassword($post);
        } catch (\Exception $e) {
            $this->sessionService->addFlash(FlashMessage::createFromParameters($e->getMessage(), FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString('/admin/user/create'));
        }

        $password = $this->passwordService->hashPassword($post['password']);

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
        $this->loginService->login($request);

        $users = $this->userRepository->findAll();

        return new Response(
            $this->twig->render('user/list.html.twig', ['users' => $users])
        );
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->loginService->login($request);

        $get = $request->get();
        $user = $this->userRepository->findById($get['id']);

        if ($request->getMethod() === Request::METHOD_GET) {
            return new Response(
                $this->twig->render('user/single.html.twig', ['title' => 'Edit user', 'user' => $user, 'formTarget'=> "/admin/user/edit?id={$user->getId()}"])
            );
        }

        $post = $request->post();

        try {
            $this->passwordService->validateNewPassword($post);
        } catch (\Exception $e) {
            $this->sessionService->addFlash(FlashMessage::createFromParameters($e->getMessage(), FlashMessage::ALERT_LEVEL_ERROR));

            return new RedirectResponse(Uri::createFromString("/admin/user/edit?id={$user->getId()}"));
        }

        $user->setPassword($this->passwordService->hashPassword($post['password']));
        $this->userRepository->persist($user);
        $this->sessionService->addFlash(FlashMessage::createFromParameters('New password saved successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/user'));
    }

    public function delete(Request $request): ResponseInterface
    {
        $this->loginService->login($request);

        $get = $request->get();
        $this->userRepository->deleteById($get['id']);
        $this->sessionService->addFlash(FlashMessage::createFromParameters('User deleted successfully', FlashMessage::ALERT_LEVEL_SUCCESS));

        return new RedirectResponse(Uri::createFromString('/admin/user'));
    }
}