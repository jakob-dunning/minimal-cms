<?php

namespace App\Model\User;

use App\Exception\AnonymousUserException;

class AnonymousUser implements UserInterface
{
    public function isAuthenticated(): bool
    {
        return false;
    }

    public function getPassword(): string
    {
        throw new AnonymousUserException();
    }

    public function getUserName(): string
    {
        throw new AnonymousUserException();
    }

    public function setSessionExpiresAt(?\DateTime $time): self
    {
        throw new AnonymousUserException();
    }

    public function getSessionExpiresAt(): ?\DateTime
    {
        throw new AnonymousUserException();
    }

    public function setSessionIdExpiresAt(?\DateTime $time): self
    {
        throw new AnonymousUserException();
    }

    public function getSessionId(): ?string
    {
        throw new AnonymousUserException();
    }

    public function setSessionId(?string $sessionId): self
    {
        throw new AnonymousUserException();
    }

    public function getId(): int
    {
        throw new AnonymousUserException();
    }

    public function setPassword(string $password): self
    {
        throw new AnonymousUserException();
    }
}