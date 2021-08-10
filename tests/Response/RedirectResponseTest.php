<?php

use App\Model\Response\RedirectResponse;
use PHPUnit\Framework\TestCase;

class RedirectResponseTest extends TestCase
{
    public function testRedirectResponseReturnsDefaultHeaders() {
        $target = '/';
        $response = new RedirectResponse($target);
        $defaultStatusCode = RedirectResponse::STATUS_TEMPORARY;

        $this->assertSame(
            ["http/1.1 {$defaultStatusCode}","Location: {$target}"], $response->getHeaders()
        );
    }

    public function testRedirectResponseReturnsHeaders() {
        $target = '/';
        $response = new RedirectResponse($target);
        $statusCode = 302;

        $this->assertSame(
            ["http/1.1 {$statusCode}","Location: {$target}"], $response->getHeaders()
        );
    }

    public function testRedirectResponseReturnsEmptyBody() {
        $response = new RedirectResponse('/');

        $this->assertSame('', $response->getBody());
    }
}