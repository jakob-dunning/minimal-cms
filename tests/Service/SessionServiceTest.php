<?php

namespace Test\Service;

use App\Service\SessionService;
use App\ValueObject\FlashMessage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\SessionService
 * @uses   \App\ValueObject\FlashMessage
 */
class SessionServiceTest extends TestCase
{
    public function testAddFlash()
    {
        $sessionMock = [];

        $flashMessageMock1 = $this->createMock(FlashMessage::class);
        $flashMessageMock2 = $this->createMock(FlashMessage::class);
        $flashMessageMock3 = $this->createMock(FlashMessage::class);

        $sessionService = new SessionService($sessionMock);
        $sessionService->addFlash($flashMessageMock1);
        $sessionService->addFlash($flashMessageMock2);
        $sessionService->addFlash($flashMessageMock3);

        $this->assertSame($sessionMock['flashes'], [$flashMessageMock1, $flashMessageMock2, $flashMessageMock3]);
    }

    public function testGetFlashes()
    {
        $sessionMock = [];

        $flashMessageMock1 = $this->createMock(FlashMessage::class);
        $flashMessageMock2 = $this->createMock(FlashMessage::class);
        $flashMessageMock3 = $this->createMock(FlashMessage::class);

        $sessionService = new SessionService($sessionMock);
        $sessionService->addFlash($flashMessageMock1);
        $sessionService->addFlash($flashMessageMock2);
        $sessionService->addFlash($flashMessageMock3);

        $this->assertSame($sessionService->getFlashes(), [$flashMessageMock1, $flashMessageMock2, $flashMessageMock3]);
    }

    public function testDeleteFlashes()
    {
        $sessionMock = [];

        $flashMessageMock1 = $this->createMock(FlashMessage::class);
        $flashMessageMock2 = $this->createMock(FlashMessage::class);
        $flashMessageMock3 = $this->createMock(FlashMessage::class);

        $sessionService = new SessionService($sessionMock);
        $sessionService->addFlash($flashMessageMock1);
        $sessionService->addFlash($flashMessageMock2);
        $sessionService->addFlash($flashMessageMock3);

        $sessionService->deleteFlashes();

        $this->assertSame($sessionMock['flashes'], []);
    }
}