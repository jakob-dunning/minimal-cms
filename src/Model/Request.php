<?php declare(strict_types=1);

namespace App\Model;

class Request
{
    public const METHOD_POST = 'POST';

    private string $requestUri;

    private array  $post;

    private string $method;

    private array  $get;

    private function __construct(string $requestUri, string $method, array $post, array $get)
    {
        $this->requestUri = $requestUri;
        $this->post       = $post;
        $this->method     = $method;
        $this->get        = $get;
    }

    public static function createFromGlobals(): self
    {
        return new self(
            parse_url(
                filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                PHP_URL_PATH
            ),
            filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? [],
            filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? []
        );
    }

    public function getUri(): string
    {
        return $this->requestUri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getSessionId(): string
    {
        return session_id();
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function getGet(): array
    {
        return $this->get;
    }
}