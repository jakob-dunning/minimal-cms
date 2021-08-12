<?php

namespace App\Service;

use App\ValueObject\FlashMessage;

class SessionService
{
    public function addFlash(FlashMessage $message): void
    {
        if (is_array($_SESSION['flashes']) === false) {
            $_SESSION['flashes'] = [];
        }

        $_SESSION['flashes'][] = $message;
    }

    public function getFlashes(): array
    {
        $messages = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);

        return $messages;
    }
}