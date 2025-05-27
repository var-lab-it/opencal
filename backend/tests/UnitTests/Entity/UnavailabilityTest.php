<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Unavailability;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class UnavailabilityTest extends TestCase
{
    public function testId(): void
    {
        $unavailability = new Unavailability();
        $refClass       = new \ReflectionClass($unavailability);
        $prop           = $refClass->getProperty('id');
        $prop->setValue($unavailability, 101);

        self::assertSame(
            101,
            $unavailability->getId(),
        );
    }

    public function testUser(): void
    {
        $userMock = $this->createMock(User::class);

        $unavailability = new Unavailability();
        $unavailability->setUser($userMock);

        self::assertSame(
            $userMock,
            $unavailability->getUser(),
        );
    }

    public function testSetUserWithNull(): void
    {
        $unavailability = new Unavailability();

        self::expectNotToPerformAssertions();

        $unavailability->setUser(null);
    }

    public function testDayOfWeek(): void
    {
        $unavailability = new Unavailability();
        $unavailability->setDayOfWeek('Tuesday');

        self::assertSame(
            'Tuesday',
            $unavailability->getDayOfWeek(),
        );
    }

    public function testStartTime(): void
    {
        $start = new DateTime('09:30');

        $unavailability = new Unavailability();
        $unavailability->setStartTime($start);

        self::assertSame(
            $start,
            $unavailability->getStartTime(),
        );
    }

    public function testEndTime(): void
    {
        $end = new DateTime('15:00');

        $unavailability = new Unavailability();
        $unavailability->setEndTime($end);

        self::assertSame(
            $end,
            $unavailability->getEndTime(),
        );
    }

    public function testFullDay(): void
    {
        $unavailability = new Unavailability();

        self::assertNull($unavailability->isFullDay());

        $unavailability->setFullDay(true);
        self::assertTrue($unavailability->isFullDay());

        $unavailability->setFullDay(false);
        self::assertFalse($unavailability->isFullDay());
    }
}
