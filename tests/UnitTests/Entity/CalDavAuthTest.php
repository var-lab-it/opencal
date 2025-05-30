<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\CalDavAuth;
use App\Entity\Event;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CalDavAuthTest extends TestCase
{
    public function testId(): void
    {
        $auth     = new CalDavAuth();
        $refClass = new \ReflectionClass($auth);
        $prop     = $refClass->getProperty('id');
        $prop->setValue($auth, 99);

        self::assertSame(
            99,
            $auth->getId(),
        );
    }

    public function testEnabled(): void
    {
        $auth = new CalDavAuth();

        $auth->setEnabled(true);
        self::assertTrue($auth->isEnabled());

        $auth->setEnabled(false);
        self::assertFalse($auth->isEnabled());
    }

    public function testBaseUri(): void
    {
        $auth = new CalDavAuth();
        $auth->setBaseUri('https://calendar.example.com');

        self::assertSame(
            'https://calendar.example.com',
            $auth->getBaseUri(),
        );
    }

    public function testUsername(): void
    {
        $auth = new CalDavAuth();
        $auth->setUsername('calendarUser');

        self::assertSame(
            'calendarUser',
            $auth->getUsername(),
        );
    }

    public function testPassword(): void
    {
        $auth = new CalDavAuth();
        $auth->setPassword('superSecret123');

        self::assertSame(
            'superSecret123',
            $auth->getPassword(),
        );
    }

    public function testUser(): void
    {
        $userMock = $this->createMock(User::class);

        $auth = new CalDavAuth();
        $auth->setUser($userMock);

        self::assertSame(
            $userMock,
            $auth->getUser(),
        );
    }

    public function testSetUserWithNull(): void
    {
        $auth = new CalDavAuth();

        self::expectNotToPerformAssertions();

        $auth->setUser(null);
    }

    public function testEvents(): void
    {
        $auth = new CalDavAuth();

        self::assertCount(0, $auth->getEvents());

        $eventMockAdd = $this->createMock(Event::class);
        $eventMockAdd
            ->expects(self::once())
            ->method('setCalDavAuth')
            ->with($auth);

        $auth->addEvent($eventMockAdd);

        self::assertCount(1, $auth->getEvents());
        self::assertSame(
            $eventMockAdd,
            $auth->getEvents()->first(),
        );

        $eventMockRemove = $this->createMock(Event::class);
        $eventMockRemove
            ->method('getCalDavAuth')
            ->willReturn($auth);

        $eventMockRemove
            ->expects(self::once())
            ->method('setCalDavAuth')
            ->with(null);

        $auth->getEvents()->add($eventMockRemove);

        $auth->removeEvent($eventMockRemove);

        self::assertNotContains($eventMockRemove, $auth->getEvents());
    }
}
