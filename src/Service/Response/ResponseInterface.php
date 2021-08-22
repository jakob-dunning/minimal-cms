<?php

namespace App\Service\Response;

interface ResponseInterface
{
    public function getHeaders(): array;

    public function getBody(): string;

    public function getStatusCode(): int;
}