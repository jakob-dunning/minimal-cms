<?php

namespace App\Service;

use App\Repository\UserRepository;
use function key_exists;
use function password_hash;
use const PASSWORD_DEFAULT;

class PasswordService
{
    private Config $config;

    private UserRepository $userRepository;

    private DateTimeService $dateTimeService;

    public function __construct(Config $config, UserRepository $userRepository, DateTimeService $dateTimeService)
    {
        $this->config          = $config;
        $this->userRepository  = $userRepository;
        $this->dateTimeService = $dateTimeService;
    }

    /**
     * @codeCoverageIgnore
     */
    public function verifyPassword(string $actual, string $expected): bool
    {
        return password_verify($actual, $expected);
    }

    public function validateNewPassword(array $post): void
    {
        if (key_exists('password', $post) === false) {
            throw new \Exception('Password cannot be empty');
        }

        if ($post['password'] === '') {
            throw new \Exception('Password cannot be empty');
        }

        if ($post['password'] !== $post['repeat-password']) {
            throw new \Exception('Passwords do not match');
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}