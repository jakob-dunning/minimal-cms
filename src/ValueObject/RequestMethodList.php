<?php

namespace App\ValueObject;

use App\Service\Request;

class RequestMethodList
{
    private array $requestMethods;

    private function __construct(array $requestMethods)
    {
        $this->requestMethods = $requestMethods;
    }

    public static function createFromArray(array $requestMethods): self
    {
        $validRequestMethods = [Request::METHOD_POST, Request::METHOD_GET];

        foreach ($requestMethods as $requestMethod) {
            if (in_array($requestMethod, $validRequestMethods) === false) {
                throw new \Exception("Not a valid http request method: {$requestMethod}");
            }
        }

        return new self($requestMethods);
    }

    public function contains(string $requestMethod): bool
    {
        return in_array($requestMethod, $this->requestMethods);
    }
}