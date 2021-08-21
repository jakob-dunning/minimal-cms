<?php

namespace Test\Service;

use App\Service\Config;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\Config
 */
class ConfigTest extends TestCase
{
    public function testGetByKey()
    {
        $config = Config::createFromArray(['testkey' => 'testvalue']);

        $this->assertSame('testvalue', $config->getByKey('testkey'));
    }

    public function testGetByKeyThrowsExceptionOnMissingKey()
    {
        $config = Config::createFromArray([]);
        $key    = 'nonexistingkey';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Config not found: ' . $key);

        $config->getByKey($key);
    }
}