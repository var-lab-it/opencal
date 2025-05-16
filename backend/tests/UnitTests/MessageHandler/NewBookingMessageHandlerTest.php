<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\MessageHandler;

use App\Entity\Event;
use App\Message\NewBookingMessage;
use App\MessageHandler\NewBookingMessageHandler;
use App\Repository\EventRepository;
use App\Service\EmailNotificationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NewBookingMessageHandlerTest extends TestCase
{
    private EventRepository&MockObject $eventRepository;
    private EmailNotificationService&MockObject $notificationService;

    protected function setUp(): void
    {
        $this->eventRepository     = $this->createMock(EventRepository::class);
        $this->notificationService = $this->createMock(EmailNotificationService::class);
    }

    public function testInvokeEventNotFound(): void
    {
        $this->eventRepository
            ->method('find')
            ->willReturn(null);

        $this->notificationService
            ->expects($this->never())
            ->method('sendNewBookingNotificationToHost');
        $this->notificationService
            ->expects($this->never())
            ->method('sendBookingConfirmationToAttendee');

        $handler = new NewBookingMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new NewBookingMessage(1));
    }

    public function testInvokeEventIsSyncedEvent(): void
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getSyncHash')
            ->willReturn('123abchashyhash');

        $this->eventRepository
            ->method('find')
            ->willReturn($eventMock);

        $this->notificationService
            ->expects($this->never())
            ->method('sendNewBookingNotificationToHost');
        $this->notificationService
            ->expects($this->never())
            ->method('sendBookingConfirmationToAttendee');

        $handler = new NewBookingMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new NewBookingMessage(1));
    }

    public function testInvokeSucceeds(): void
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getSyncHash')
            ->willReturn(null);

        $this->eventRepository
            ->method('find')
            ->willReturn($eventMock);

        $this->notificationService
            ->expects($this->once())
            ->method('sendNewBookingNotificationToHost');
        $this->notificationService
            ->expects($this->once())
            ->method('sendBookingConfirmationToAttendee');

        $handler = new NewBookingMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new NewBookingMessage(1));
    }
}
