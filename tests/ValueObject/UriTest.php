<?php

use App\ValueObject\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Uri
 */
class UriTest extends TestCase
{
    public function testCreate()
    {
        $path      = '/ghdfhgfasdd';
        $uriString = "https://hjagsdjgj.com{$path}";
        $uri       = Uri::createFromString($uriString);

        $this->assertSame($path, $uri->getPath());
        $this->assertSame($uriString, (string)$uri);
    }

    public function testCreateThrowsExceptionOnInvalidUri()
    {
        $uri = 'http:///example.com';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Not a valid uri: {$uri}");

        Uri::createFromString($uri);
    }
}