<?php

namespace App\Service;

class Config
{
    private array $items;

    public function __construct(string $path)
    {
        $json = file_get_contents($path);

        $this->items = json_decode($json, true);
    }

    public function getByKey(string $key)
    {
        if(key_exists($key, $this->items) === false) {
            throw new \Exception('Config not found: ' . $key);
        }

        return $this->items[$key];
    }
}