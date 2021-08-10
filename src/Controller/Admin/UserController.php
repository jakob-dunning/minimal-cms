<?php

namespace App\Controller\Admin;

use App\Model\Request;
use App\Model\Response\RedirectResponse;
use App\Model\Response\Response;
use App\Model\Response\ResponseInterface;
use App\Repository\UserRepository;
use App\Service\Authentication;
use Twig\Environment;

class UserController
{
    private UserRepository $userRepository;

    private Environment $twig;

    private Authentication $authentication;

    public function __construct(UserRepository $userRepository, Environment $twig, Authentication $authentication)
    {
        $this->userRepository = $userRepository;
        $this->twig           = $twig;
        $this->authentication = $authentication;
    }

    public function add(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return new Response($this->twig->render('user/single.html.twig', ['activeUri' => $request->getUri()]));
        }

        $post = $request->getPost();

        if ($post['password'] !== $post['repeat-password']) {
            return new Response($this->twig->render('user/single.html.twig',
                                                    ['activeUri' => $request->getUri(), 'errors' => ['Passwords do not match']]));
        }

        $password = password_hash($post['password'], PASSWORD_DEFAULT);
        $this->userRepository->createUser($post['user'], $password);

        return new RedirectResponse('/admin/user');
    }

    public function list(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $users = $this->userRepository->findAll();

        return new Response(
            $this->twig->render('user/list.html.twig', ['users' => $users, 'activeUri' => $request->getUri()])
        );
    }

    public function edit(Request $request): ResponseInterface
    {
        $this->authentication->authenticateUser($request);

        $get  = $request->getGet();
        $user = $this->userRepository->findById($get['id']);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return new Response(
                $this->twig->render('user/single.html.twig', ['title' => 'Edit user', 'user' => $user, 'activeUri' => $request->getUri()])
            );
        }

        $post = $request->getPost();

        if ($post['password'] !== $post['repeat-password']) {
            return new Response(
                $this->twig->render('user/single.html.twig',['title' => 'Edit user', 'user' => $user, 'activeUri' => $request->getUri(), 'errors' => ['Passwords do not match']])
            );
        }

        if($post['password'] !== '') {
            $user->setPassword(password_hash($post['password'], PASSWORD_DEFAULT));
        }

        $this->userRepository->persist($user);

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