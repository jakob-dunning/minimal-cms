<?php

use App\Service\Request;
use App\ValueObject\RequestMethodList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\RequestMethodList
 * @uses   \App\Service\Request
 */
class RequestMethodListTest extends TestCase
{
    public function testCreateFromParameters()
    {
        $requestMethods    = [Request::METHOD_POST];
        $requestMethodList = RequestMethodList::createFromArray($requestMethods);

        $this->assertSame(true, $requestMethodList->contains(Request::METHOD_POST));
    }

    public function testCreateFromParametersThrowsExceptionOnInvalidRequestMethod()
    {
        $requestMethod = 'eat_meat';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Not a valid http request method: {$requestMethod}");

        RequestMethodList::createFromArray([$requestMethod]);
    }
}