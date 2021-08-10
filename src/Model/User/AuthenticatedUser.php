<?php

namespace App\Model\User;

class AuthenticatedUser implements UserInterface
{
    private string    $username;

    private string    $password;

    private ?string $sessionId;

    private ?\DateTime $sessionExpiresAt;

    private int        $id;

    public function __construct(int $id, string $username, string $password, ?string $sessionId, ?\DateTime $sessionExpiresAt)
    {
        $this->username         = $username;
        $this->sessionId        = $sessionId;
        $this->sessionExpiresAt = $sessionExpiresAt;
        $this->password         = $password;
        $this->id               = $id;
    }

    public function isAuthenticated(): bool
    {
        if (new \DateTime() > $this->sessionExpiresAt) {
            return false;
        }

        return true;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUserName(): string
    {
        return $this->username;
    }

    public function setSessionExpiresAt(?\DateTime $time): self
    {
        $this->sessionExpiresAt = $time;

        return $this;
    }

    public function getSessionExpiresAt(): ?\DateTime
    {
        return $this->sessionExpiresAt;
    }

    public function setSessionIdExpiresAt(?\DateTime $time): self
    {
        $this->sessionExpiresAt = $time;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}