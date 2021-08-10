<?php

namespace App\Model\Response;

interface ResponseInterface
{
    public function getHeaders(): array;

    public function getBody(): string;
}