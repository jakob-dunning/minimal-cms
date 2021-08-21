<?php

namespace Test\Service\FileLoader;

use App\Service\FileLoader\JsonFileLoader;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\FileLoader\JsonFileLoader
 */
class JsonFileLoaderTest extends TestCase
{
    public function testGetArrayData()
    {
        $jsonArrayFileLoader = new JsonFileLoader(__DIR__ . '/../../data/testArray.json');

        $this->assertSame(['hase', 'Brot'], $jsonArrayFileLoader->getData());
    }

    public function testGetObjectData()
    {
        $jsonArrayFileLoader = new JsonFileLoader(__DIR__ . '/../../data/testObject.json');

        $this->assertSame(['hase' => 'Brot'], $jsonArrayFileLoader->getData());
    }

    public function testThrowsExceptionOnBadData()
    {
        $this->expectException(\JsonException::class);

        new JsonFileLoader(__DIR__ . '/../../data/invalidJson.json');
    }
}