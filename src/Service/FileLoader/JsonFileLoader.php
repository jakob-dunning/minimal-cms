<?php

namespace App\Service\FileLoader;

class JsonFileLoader
{
    private $data;

    public function __construct(string $path)
    {
        $fileContent = file_get_contents($path);

        $data = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}