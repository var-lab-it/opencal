<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\CalDavAuth;
use App\Entity\Event;
use App\Entity\EventType;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class EventTest extends TestCase
{
    public function testId(): void
    {
        $event    = new Event();
        $refClass = new \ReflectionClass($event);
        $prop     = $refClass->getProperty('id');
        $prop->setValue($event, 777);

        self::assertSame(
            777,
            $event->getId(),
        );
    }

    public function testEventType(): void
    {
        $eventTypeMock = $this->createMock(EventType::class);

        $event = new Event();
        $event->setEventType($eventTypeMock);

        self::assertSame(
            $eventTypeMock,
            $event->getEventType(),
        );
    }

    public function testStartTime(): void
    {
        $startTime = new DateTime('10:00');

        $event = new Event();
        $event->setStartTime($startTime);

        self::assertSame(
            $startTime,
            $event->getStartTime(),
        );
    }

    public function testEndTime(): void
    {
        $endTime = new DateTime('12:00');

        $event = new Event();
        $event->setEndTime($endTime);

        self::assertSame(
            $endTime,
            $event->getEndTime(),
        );
    }

    public function testDay(): void
    {
        $day = new DateTime('2025-05-01');

        $event = new Event();
        $event->setDay($day);

        self::assertSame(
            $day,
            $event->getDay(),
        );
    }

    public function testParticipantName(): void
    {
        $event = new Event();
        $event->setParticipantName('Alice');

        self::assertSame(
            'Alice',
            $event->getParticipantName(),
        );
    }

    public function testParticipantEmail(): void
    {
        $event = new Event();
        $event->setParticipantEmail('alice@example.com');

        self::assertSame(
            'alice@example.com',
            $event->getParticipantEmail(),
        );
    }

    public function testParticipantMessage(): void
    {
        $event = new Event();
        $event->setParticipantMessage('See you there!');

        self::assertSame(
            'See you there!',
            $event->getParticipantMessage(),
        );
    }

    public function testCancellationHash(): void
    {
        $event = new Event();
        $event->setCancellationHash('abc123');

        self::assertSame(
            'abc123',
            $event->getCancellationHash(),
        );
    }

    public function testCanceledByAttendee(): void
    {
        $event = new Event();

        self::assertNull($event->isCancelledByAttendee());

        $event->setCanceledByAttendee(true);
        self::assertTrue($event->isCancelledByAttendee());

        $event->setCanceledByAttendee(false);
        self::assertFalse($event->isCancelledByAttendee());
    }

    public function testSyncHash(): void
    {
        $event = new Event();
        $event->setSyncHash('sync-xyz');

        self::assertSame(
            'sync-xyz',
            $event->getSyncHash(),
        );
    }

    public function testCalDavAuth(): void
    {
        $authMock = $this->createMock(CalDavAuth::class);

        $event = new Event();
        $event->setCalDavAuth($authMock);

        self::assertSame(
            $authMock,
            $event->getCalDavAuth(),
        );
    }

    public function testSetCalDavAuthNull(): void
    {
        $event = new Event();

        self::expectNotToPerformAssertions();

        $event->setCalDavAuth(null);
    }
}
