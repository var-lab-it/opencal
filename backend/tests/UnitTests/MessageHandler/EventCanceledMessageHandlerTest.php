<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\MessageHandler;

use App\Entity\Event;
use App\Message\EventCanceledMessage;
use App\MessageHandler\EventCanceledMessageHandler;
use App\Repository\EventRepository;
use App\Service\EmailNotificationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventCanceledMessageHandlerTest extends TestCase
{
    private EventRepository&MockObject $eventRepository;
    private EmailNotificationService&MockObject $notificationService;

    protected function setUp(): void
    {
        $this->eventRepository     = $this->createMock(EventRepository::class);
        $this->notificationService = $this->createMock(EmailNotificationService::class);
    }

    public function testInvokeEventFound(): void
    {
        $eventMock = $this->createMock(Event::class);

        $this->eventRepository
            ->method('find')
            ->willReturn($eventMock);

        $this->notificationService
            ->expects($this->once())
            ->method('sendBookingCanceledNotificationToAHost');

        $handler = new EventCanceledMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new EventCanceledMessage(1));
    }

    public function testInvokeEventNotFound(): void
    {
        $this->eventRepository
            ->method('find')
            ->willReturn(null);

        $this->notificationService
            ->expects($this->never())
            ->method('sendBookingCanceledNotificationToAHost');

        $handler = new EventCanceledMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new EventCanceledMessage(1));
    }
}
