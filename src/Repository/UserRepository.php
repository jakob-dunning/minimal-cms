<?php

namespace App\Repository;

use App\Model\User\AnonymousUser;
use App\Model\User\AuthenticatedUser;
use App\Model\User\UserInterface;
use App\Service\Database\RelationalDatabaseInterface;

class UserRepository
{
    private RelationalDatabaseInterface $database;

    public function __construct(RelationalDatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findBySessionId(string $sessionId): UserInterface
    {
        $data = $this->database->select('*', 'user', ['session_id' => $sessionId]);

        if (count($data) === 0) {
            return new AnonymousUser();
        }

        $userData = reset($data);

        return new AuthenticatedUser($userData['id'], $userData['username'], $userData['password'], $userData['session_id'],
                                     new \DateTime($userData['session_expires_at']));
    }

    public function findByUsername(string $username): UserInterface
    {
        $data = $this->database->select('*', 'user', ['username' => $username]);

        if (count($data) === 0) {
            return new AnonymousUser();
        }

        $userData = reset($data);

        return new AuthenticatedUser($userData['id'], $userData['username'], $userData['password'], $userData['session_id'],
                                     new \DateTime($userData['session_expires_at']));
    }

    public function persist(UserInterface $user): void
    {
        $sessionExpiresAt = ($user->getSessionExpiresAt() === null)
            ? null
            : $user->getSessionExpiresAt()->format('Y-m-d H:i:s');

        $fields = ['session_id' => $user->getSessionId(), 'session_expires_at' => $sessionExpiresAt, 'password' => $user->getPassword()];

        $this->database->update(
            'user',
            $fields, ['id' => $user->getId()]);
    }

    public function createUser(string $username, string $password): void
    {
        $this->database->insert('user', ['username' => $username, 'password' => $password]);
    }

    public function findAll(): array
    {
        $data = $this->database->select('*', 'user', []);

        if ($data === false) {
            return [];
        }

        $users = [];

        foreach ($data as $userData) {
            $users[] = new AuthenticatedUser($userData['id'], $userData['username'], $userData['password'], $userData['session_id'],
                                             new \DateTime($userData['session_expires_at']));
        }

        return $users;
    }

    public function findById(int $id): UserInterface
    {
        $data     = $this->database->select('*', 'user', ['id' => $id]);
        $userData = reset($data);

        return new AuthenticatedUser($userData['id'], $userData['username'], $userData['password'], $userData['session_id'],
                                     new \DateTime($userData['session_expires_at']));
    }

    public function deleteById(int $id): void
    {
        $this->database->delete(
            'user',
            ['id' => $id]
        );
    }
}