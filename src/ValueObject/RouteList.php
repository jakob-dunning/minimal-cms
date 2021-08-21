<?php

namespace App\ValueObject;

class RouteList
{
    private $routes = [];

    public static function createFromArray(array $routes) : self
    {
        $routeList = new self();

        foreach ($routes as $route) {
            $routeList->add(
                new Route(
                    Uri::createFromString($route['uri']),
                    $route['controller'],
                    $route['method'],
                    RequestMethodList::createFromArray($route['allowedRequestMethods'])
                )
            );
        }

        return $routeList;
    }

    public function add(Route $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function all(): \ArrayIterator
    {
        return new \ArrayIterator($this->routes);
    }

    public function findByPath(string $path): ?Route
    {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($route->getUri()->getPath() === $path) {
                return $route;
            }
        }

        return null;
    }
}