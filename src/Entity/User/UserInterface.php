<?php

namespace App\Entity\User;

interface UserInterface
{
    public function getPassword(): string;

    public function getUserName(): string;

    public function getSessionExpiresAt(): ?\DateTime;

    public function setSessionExpiresAt(?\DateTime $time): self;

    public function getSessionId(): ?string;

    public function setSessionId(?string $sessionId): self;

    public function getId(): int;

    public function setPassword(string $password): self;
}
