<?php

namespace App\ValueObject;

/**
 * @codeCoverageIgnore
 */
class Route
{
    private Uri $uri;

    private string $controller;

    private string $method;

    private RequestMethodList $allowedRequestMethods;

    public function __construct(Uri $uri, string $controller, string $method, RequestMethodList $allowedRequestMethods)
    {
        $this->uri                   = $uri;
        $this->controller            = $controller;
        $this->method                = $method;
        $this->allowedRequestMethods = $allowedRequestMethods;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAllowedRequestMethods(): RequestMethodList
    {
        return $this->allowedRequestMethods;
    }
}