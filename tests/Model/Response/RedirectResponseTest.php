<?php

use App\Model\Response\RedirectResponse;
use App\ValueObject\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Response\RedirectResponse
 * @uses \App\Model\Response\ResponseInterface
 * @uses   \App\ValueObject\Uri
 */
class RedirectResponseTest extends TestCase
{
    public function testRedirectResponseReturnsDefaultHeaders()
    {
        $target            = '/';
        $response          = new RedirectResponse(Uri::createFromString($target));
        $defaultStatusCode = RedirectResponse::STATUS_TEMPORARY;

        $this->assertSame(["http/1.1 {$defaultStatusCode}", "Location: {$target}"], $response->getHeaders());
    }

    public function testRedirectResponseReturnsHeaders()
    {
        $target     = '/';
        $response   = new RedirectResponse(Uri::createFromString($target));
        $statusCode = RedirectResponse::STATUS_TEMPORARY;

        $this->assertSame(["http/1.1 {$statusCode}", "Location: {$target}"], $response->getHeaders());
    }

    public function testRedirectResponseReturnsEmptyBody()
    {
        $response = new RedirectResponse(Uri::createFromString('/'));

        $this->assertSame('', $response->getBody());
    }
}