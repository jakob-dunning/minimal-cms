<?php

namespace App\Repository;

use App\Entity\User\User;
use App\Entity\User\UserInterface;
use App\Exception\NotAuthenticatedException;
use App\Exception\UserNotFoundException;
use App\Service\Database\RelationalDatabaseInterface;
use App\Service\DateTimeService;

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
            throw new UserNotFoundException();
        }

        $userData = reset($data);

        return User::createFromArray($userData);
    }

    public function findByUsername(string $username): UserInterface
    {
        $data = $this->database->select(['*'], 'user', ['username' => $username]);

        if (count($data) === 0) {
            throw new UserNotFoundException();
        }

        $userData = reset($data);

        return User::createFromArray($userData);
    }

    public function persist(UserInterface $user): void
    {
        $sessionExpiresAt = ($user->getSessionExpiresAt() === null)
            ? null
            : $user->getSessionExpiresAt()->format(DateTimeService::FORMAT_SQL);

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

        $users = [];

        foreach ($data as $userData) {
            $users[] = User::createFromArray($userData);
        }

        return $users;
    }

    public function findById(int $id): ?UserInterface
    {
        $data = $this->database->select(['*'], 'user', ['id' => $id]);

        if (count($data) === 0) {
            throw new UserNotFoundException();
        }

        $userData = reset($data);

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