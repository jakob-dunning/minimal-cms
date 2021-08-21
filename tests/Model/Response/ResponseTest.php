<?php

use App\Model\Response\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Response\Response
 * @uses   \App\Model\Response\ResponseInterface
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
}