<?php

namespace App\Model;

class RouteList
{
    private $routes = [];

    public function add(Page $route): void
    {
        $this->routes[] = $route;
    }

    public function all(): \ArrayIterator
    {
        return new \ArrayIterator($this->routes);
    }
}