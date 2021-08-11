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
use Twig\Environment;

class UserController
{
    private UserRepository $userRepository;

    private Environment $twig;

    private AuthenticationService $authentication;

    private SessionService $sessionService;

    public function __construct(
        UserRepository $userRepository,
        Environment $twig,
        AuthenticationService $authentication,
        SessionService $sessionService
    ) {
        $this->userRepository = $userRepository;
        $this->twig           = $twig;
        $this->authentication = $authentication;
        $this->sessionService = $sessionService;
    }

    public function add(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return new Response($this->twig->render('user/single.html.twig'));
        }

        $post = $request->getPost();

        if ($post['password'] !== $post['repeat-password']) {
            return new Response($this->twig->render('user/single.html.twig',
                                                    ['errors' => ['Passwords do not match']]));
        }

        $password = password_hash($post['password'], PASSWORD_DEFAULT);
        $this->userRepository->create($post['user'], $password);

        return new RedirectResponse('/admin/user');
    }

    public function list(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $users = $this->userRepository->findAll();

        return new Response(
            $this->twig->render('user/list.html.twig', ['users' => $users])
        );
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $get  = $request->getGet();
        $user = $this->userRepository->findById($get['id']);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return new Response(
                $this->twig->render('user/single.html.twig', ['title' => 'Edit user', 'user' => $user])
            );
        }

        $post = $request->getPost();

        if ($post['password'] === '') {
            $this->sessionService->setFlash(
                new FlashMessage('Password cannot be empty', FlashMessage::SEVERITY_LEVEL_ERROR)
            );

            return new Response(
                $this->twig->render('user/single.html.twig', ['title' => 'Edit user', 'user' => $user])
            );
        }

        if ($post['password'] !== $post['repeat-password']) {
            $this->sessionService->setFlash(
                new FlashMessage('Passwords do not match', FlashMessage::SEVERITY_LEVEL_ERROR)
            );

            return new Response(
                $this->twig->render('user/single.html.twig', ['title' => 'Edit user', 'user' => $user])
            );
        }

        $user->setPassword(password_hash($post['password'], PASSWORD_DEFAULT));
        $this->userRepository->persist($user);
        $this->sessionService->setFlash(
            new FlashMessage('New password saved successfully', FlashMessage::SEVERITY_LEVEL_SUCCESS)
        );

        return new RedirectResponse('/admin/user');
    }

    public function delete(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $get = $request->getGet();
        $this->userRepository->deleteById($get['id']);

        return new RedirectResponse('/admin/user');
    }
}