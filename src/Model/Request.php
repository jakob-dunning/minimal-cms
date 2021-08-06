<?php declare(strict_types=1);

namespace App\Model;

class Request
{
    private string $requestUri;

    private array $get;

    private function __construct(string $requestUri, array $get)
    {
        $this->requestUri = $requestUri;
        $this->get        = $get;
    }

    public static function createFromGlobals(): self
    {
        return new self(
            filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? []
        );
    }

    public function getUri(): string
    {
        return $this->requestUri;
    }
}