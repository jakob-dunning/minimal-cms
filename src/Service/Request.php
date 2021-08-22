<?php declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Uri;

/**
 * @codeCoverageIgnore
 */
class Request
{
    public const METHOD_POST = 'POST';

    public const METHOD_GET = 'GET';

    private Uri $requestUri;

    private array  $post;

    private string $method;

    private array  $get;

    private function __construct(Uri $requestUri, string $method, array $post, array $get)
    {
        $this->requestUri = $requestUri;
        $this->post       = $post;
        $this->method     = $method;
        $this->get        = $get;
    }

    public static function createFromGlobals(): self
    {
        return new self(
            Uri::createFromString(filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? [],
            filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? []
        );
    }

    public function getUri(): Uri
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

    public function post(): array
    {
        return $this->post;
    }

    public function get(): array
    {
        return $this->get;
    }
}