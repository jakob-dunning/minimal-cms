<?php

namespace Test;

use App\Service\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetByKey() {
        $config = new Config(__DIR__ . '/../data/mockconfig.json');

        $this->assertSame('testvalue', $config->getByKey('testkey'));
    }

    public function testGetByKeyThrowsExceptionOnMissingKey() {
        $config = new Config(__DIR__ . '/../data/mockconfig.json');
        $key = 'nonexistingkey';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Config not found: ' . $key);

        $config->getByKey($key);
    }
}