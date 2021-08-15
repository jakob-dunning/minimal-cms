<?php

namespace App\Repository;

use App\Model\User\User;
use App\Model\User\UserInterface;
use App\Service\Database\RelationalDatabaseInterface;

class UserRepository
{
    private RelationalDatabaseInterface $database;

    public function __construct(RelationalDatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findBySessionId(string $sessionId): ?UserInterface
    {
        $data = $this->database->select(['*'], 'user', ['session_id' => $sessionId]);

        if (count($data) === 0) {
            return null;
        }

        $userData = reset($data);
        $userData['session_expires_at'] = new \DateTime($userData['session_expires_at']);

        return User::createFromArray($userData);
    }

    public function findByUsername(string $username): ?UserInterface
    {
        $data = $this->database->select(['*'], 'user', ['username' => $username]);

        if (count($data) === 0) {
            return null;
        }

        $userData = reset($data);
        $userData['session_expires_at'] = new \DateTime($userData['session_expires_at']);

        return User::createFromArray($userData);
    }

    public function persist(UserInterface $user): void
    {
        $sessionExpiresAt = ($user->getSessionExpiresAt() === null)
            ? null
            : $user->getSessionExpiresAt()->format('Y-m-d H:i:s');

        $fields = ['session_id' => $user->getSessionId(), 'session_expires_at' => $sessionExpiresAt, 'password' => $user->getPassword()];

        $this->database->update(
            'user',
            $fields,
            ['id' => $user->getId()]
        );
    }

    public function create(string $username, string $password): void
    {
        $this->database->insert('user', ['username' => $username, 'password' => $password]);
    }

    public function findAll(): array
    {
        $data = $this->database->select(['*'], 'user');

        if ($data === false) {
            return [];
        }

        $users = [];

        foreach ($data as $userData) {
            $userData['session_expires_at'] = new \DateTime($userData['session_expires_at']);

            $users[] = User::createFromArray($userData);
        }

        return $users;
    }

    public function findById(int $id): UserInterface
    {
        $data     = $this->database->select(['*'], 'user', ['id' => $id]);
        $userData = reset($data);
        $userData['session_expires_at'] = new \DateTime($userData['session_expires_at']);

        return User::createFromArray($userData);
    }

    public function deleteById(int $id): void
    {
        $this->database->delete(
            'user',
            ['id' => $id]
        );
    }
}