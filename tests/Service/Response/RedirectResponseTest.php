<?php

use App\Service\Response\RedirectResponse;
use App\ValueObject\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\Response\RedirectResponse
 * @uses \App\Service\Response\ResponseInterface
 * @uses   \App\ValueObject\Uri
 */
class RedirectResponseTest extends TestCase
{
    public function testReturnsDefaultHeaders()
    {
        $target            = '/';
        $response          = new RedirectResponse(Uri::createFromString($target));
        $defaultStatusCode = RedirectResponse::STATUS_TEMPORARY;

        $this->assertSame(["http/1.1 {$defaultStatusCode}", "Location: {$target}"], $response->getHeaders());
    }

    public function testReturnsHeaders()
    {
        $target     = '/';
        $response   = new RedirectResponse(Uri::createFromString($target));
        $statusCode = RedirectResponse::STATUS_TEMPORARY;

        $this->assertSame(["http/1.1 {$statusCode}", "Location: {$target}"], $response->getHeaders());
    }

    public function testReturnsEmptyBody()
    {
        $response = new RedirectResponse(Uri::createFromString('/'));

        $this->assertSame('', $response->getBody());
    }

    public function testReturnsStatusCode() {
        $statusCode = 301;
        $response = new RedirectResponse(Uri::createFromString('/'), $statusCode);

        $this->assertSame($statusCode, $response->getStatusCode());
    }
}