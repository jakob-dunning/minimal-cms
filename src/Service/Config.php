<?php

namespace App\Service;

class Config
{
    private array $items;

    private function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function createFromArray(array $items) : self
    {
        return new self($items);
    }

    public function getByKey(string $key)
    {
        if (key_exists($key, $this->items) === false) {
            throw new \Exception('Config not found: ' . $key);
        }

        return $this->items[$key];
    }
}