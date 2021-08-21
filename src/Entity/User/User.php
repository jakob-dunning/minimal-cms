<?php

namespace App\Entity\User;

/**
 * @codeCoverageIgnore
 */
class User implements UserInterface
{
    private string    $username;

    private string    $password;

    private ?string $sessionId;

    private ?\DateTime $sessionExpiresAt;

    private int        $id;

    private function __construct(int $id, string $username, string $password, ?string $sessionId, ?\DateTime $sessionExpiresAt)
    {
        $this->username         = $username;
        $this->sessionId        = $sessionId;
        $this->sessionExpiresAt = $sessionExpiresAt;
        $this->password         = $password;
        $this->id               = $id;
    }

    public static function createFromArray($userData): self
    {
        return new self(
            $userData['id'],
            $userData['username'],
            $userData['password'],
            $userData['session_id'],
            ($userData['session_expires_at'] === null)
                ? null
                : new \DateTime($userData['session_expires_at'])

        );
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUserName(): string
    {
        return $this->username;
    }

    public function getSessionExpiresAt(): ?\DateTime
    {
        return $this->sessionExpiresAt;
    }

    public function setSessionExpiresAt(?\DateTime $time): self
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