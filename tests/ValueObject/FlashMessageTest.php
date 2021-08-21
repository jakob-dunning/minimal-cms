<?php

use App\ValueObject\FlashMessage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\FlashMessage
 */
class FlashMessageTest extends TestCase
{
    public function testCreateFromParameters() {
        $message = 'ulgahefuh';
        $alertLevel = FlashMessage::ALERT_LEVEL_ERROR;

        $flashMessage = FlashMessage::createFromParameters($message, $alertLevel);

        $this->assertSame($message, $flashMessage->getMessage());
        $this->assertSame($alertLevel, $flashMessage->getAlertLevel());
    }

    public function testCreateFromParametersThrowsExceptionOnInvalidAlertlevel() {
        $message = 'ulgahefuh';
        $alertLevel = 'pips';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Not a valid alert level: {$alertLevel}");

        FlashMessage::createFromParameters($message, $alertLevel);
    }

}