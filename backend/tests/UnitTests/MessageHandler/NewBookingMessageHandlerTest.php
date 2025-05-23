<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\MessageHandler;

use App\Entity\Event;
use App\Message\NewBookingMessage;
use App\MessageHandler\NewBookingMessageHandler;
use App\Repository\EventRepository;
use App\Service\Notification\Email\NewBookingToAttendeeEmailNotificationService;
use App\Service\Notification\Email\NewBookingToHostEmailNotificationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NewBookingMessageHandlerTest extends TestCase
{
    private EventRepository&MockObject $eventRepository;
    private NewBookingToHostEmailNotificationService&MockObject $newBookingToHostEmailNotificationServiceMock;
    private NewBookingToAttendeeEmailNotificationService&MockObject $newBookingToAttendeeEmailNotificationServiceMock;

    protected function setUp(): void
    {
        $this->eventRepository                                  = $this->createMock(EventRepository::class);
        $this->newBookingToHostEmailNotificationServiceMock     = $this->createMock(
            NewBookingToHostEmailNotificationService::class,
        );
        $this->newBookingToAttendeeEmailNotificationServiceMock = $this->createMock(
            NewBookingToAttendeeEmailNotificationService::class,
        );
    }

    public function testInvokeEventNotFound(): void
    {
        $this->eventRepository
            ->method('find')
            ->willReturn(null);

        $this->newBookingToHostEmailNotificationServiceMock
            ->expects($this->never())
            ->method('sendNotification');
        $this->newBookingToAttendeeEmailNotificationServiceMock
            ->expects($this->never())
            ->method('sendNotification');

        $handler = new NewBookingMessageHandler(
            $this->eventRepository,
            $this->newBookingToHostEmailNotificationServiceMock,
            $this->newBookingToAttendeeEmailNotificationServiceMock,
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

        $this->newBookingToHostEmailNotificationServiceMock
            ->expects($this->never())
            ->method('sendNotification');
        $this->newBookingToAttendeeEmailNotificationServiceMock
            ->expects($this->never())
            ->method('sendNotification');

        $handler = new NewBookingMessageHandler(
            $this->eventRepository,
            $this->newBookingToHostEmailNotificationServiceMock,
            $this->newBookingToAttendeeEmailNotificationServiceMock,
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

        $this->newBookingToHostEmailNotificationServiceMock
            ->expects($this->once())
            ->method('sendNotification');
        $this->newBookingToAttendeeEmailNotificationServiceMock
            ->expects($this->once())
            ->method('sendNotification');

        $handler = new NewBookingMessageHandler(
            $this->eventRepository,
            $this->newBookingToHostEmailNotificationServiceMock,
            $this->newBookingToAttendeeEmailNotificationServiceMock,
        );

        $handler->__invoke(new NewBookingMessage(1));
    }
}
