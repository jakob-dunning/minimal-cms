<?php

use App\Service\Response\Response;
use App\ValueObject\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\Response\Response
 * @uses   \App\Service\Response\ResponseInterface
 * @uses   \App\ValueObject\Uri
 */
class ResponseTest extends TestCase
{
    public function testResponseReturnsDefaultHeaders()
    {
        $response          = new Response('jhf');
        $defaultStatusCode = Response::STATUS_OK;

        $this->assertSame(["http/1.1 {$defaultStatusCode}"], $response->getHeaders());
    }

    public function testResponseReturnsHeaders()
    {
        $content    = 'klsudfhuhsfuo';
        $statusCode = 210;
        $response   = new Response($content, $statusCode);

        $this->assertSame(["http/1.1 {$statusCode}"], $response->getHeaders());
    }

    public function testResponseReturnsBody()
    {
        $content  = 'jakshdoiuhasuiod';
        $response = new Response($content);

        $this->assertSame($content, $response->getBody());
    }

    public function testReturnsStatusCode() {
        $statusCode = 301;
        $response = new Response(Uri::createFromString('/'), $statusCode);

        $this->assertSame($statusCode, $response->getStatusCode());
    }
}