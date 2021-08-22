<?php

namespace App\Service;

use App\ValueObject\FlashMessage;

class Session
{
    private array $session;

    public function __construct(array &$session)
    {
        $this->session = &$session;
    }

    public function addFlash(FlashMessage $message): void
    {
        if (is_array($this->session['flashes']) === false) {
            $this->session['flashes'] = [];
        }

        $this->session['flashes'][] = $message;
    }

    public function getFlashes(): array
    {
        return $this->session['flashes'] ?? [];
    }

    public function deleteFlashes(): void
    {
        $this->session['flashes'] = [];
    }
}