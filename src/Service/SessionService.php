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

    public function deleteFlashes(): void
    {
        unset($_SESSION['flashes']);
    }
}