<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EventTypeTest extends TestCase
{
    public function testId(): void
    {
        $eventType = new EventType();
        $refClass  = new \ReflectionClass($eventType);
        $prop      = $refClass->getProperty('id');
        $prop->setValue($eventType, 42);

        self::assertSame(
            42,
            $eventType->getId(),
        );
    }

    public function testName(): void
    {
        $eventType = new EventType();
        $eventType->setName('Consultation');

        self::assertSame(
            'Consultation',
            $eventType->getName(),
        );
    }

    public function testDescription(): void
    {
        $eventType = new EventType();
        $eventType->setDescription('A short video call.');

        self::assertSame(
            'A short video call.',
            $eventType->getDescription(),
        );
    }

    public function testDuration(): void
    {
        $eventType = new EventType();
        $eventType->setDuration(30);

        self::assertSame(
            30,
            $eventType->getDuration(),
        );
    }

    public function testSlug(): void
    {
        $eventType = new EventType();
        $eventType->setSlug('consultation');

        self::assertSame(
            'consultation',
            $eventType->getSlug(),
        );
    }

    public function testHost(): void
    {
        $userMock = $this->createMock(User::class);

        $eventType = new EventType();
        $eventType->setHost($userMock);

        self::assertSame(
            $userMock,
            $eventType->getHost(),
        );
    }

    public function testSetHostWithNull(): void
    {
        $eventType = new EventType();

        self::expectNotToPerformAssertions();

        $eventType->setHost(null);
    }

    public function testEvents(): void
    {
        $eventType = new EventType();

        self::assertCount(0, $eventType->getEvents());

        $eventMockAdd = $this->createMock(Event::class);
        $eventMockAdd
            ->expects(self::once())
            ->method('setEventType')
            ->with($eventType);

        $eventType->addEvent($eventMockAdd);

        self::assertCount(1, $eventType->getEvents());
        self::assertSame(
            $eventMockAdd,
            $eventType->getEvents()->first(),
        );

        $eventMockRemove = $this->createMock(Event::class);
        $eventMockRemove
            ->method('getEventType')
            ->willReturn($eventType);

        $eventMockRemove
            ->expects(self::once())
            ->method('setEventType')
            ->with(null);

        $eventType->getEvents()->add($eventMockRemove);

        $eventType->removeEvent($eventMockRemove);

        self::assertNotContains($eventMockRemove, $eventType->getEvents());
    }
}
