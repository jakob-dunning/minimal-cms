<?php

namespace App\Model\User;

interface UserInterface
{
    public function isAuthenticated(): bool;

    public function getPassword(): string;

    public function getUserName(): string;

    public function setSessionExpiresAt(?\DateTime $time): self;

    public function getSessionExpiresAt(): ?\DateTime;

    public function setSessionIdExpiresAt(?\DateTime $time): self;

    public function getSessionId(): ?string;

    public function setSessionId(?string $sessionId): self;

    public function getId(): int;

    public function setPassword(string $password): self;
}
