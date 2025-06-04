<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Availability;

use App\Availability\AvailabilityService;
use App\Entity\Availability;
use App\Entity\EventType;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use App\Repository\EventRepository;
use App\Repository\UnavailabilityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class AvailabilityServiceTest extends TestCase
{
    private AvailabilityRepository&MockObject $availabilityRepositoryMock;
    private UnavailabilityRepository&MockObject $unavailabilityRepositoryMock;
    private EventRepository&MockObject $eventRepositoryMock;
    private AvailabilityService $service;

    protected function setUp(): void
    {
        $this->availabilityRepositoryMock   = $this->createMock(AvailabilityRepository::class);
        $this->unavailabilityRepositoryMock = $this->createMock(UnavailabilityRepository::class);
        $this->eventRepositoryMock          = $this->createMock(EventRepository::class);
        $this->service                      = new AvailabilityService(
            $this->unavailabilityRepositoryMock,
            $this->availabilityRepositoryMock,
            $this->eventRepositoryMock,
        );
    }

    public function testGetDayAvailability(): void
    {
        $day           = new DateTime('2023-11-10');
        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('12:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        $expected = [
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '11:00', 'end' => '12:00'],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetDayAvailabilityWithUnavailabilities(): void
    {
        $day           = new DateTime('2023-11-10');
        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('17:00'));

        $unavailabilityMock = $this->createMock(Unavailability::class);
        $unavailabilityMock->method('isFullDay')->willReturn(false);
        $unavailabilityMock->method('getStartTime')->willReturn(new DateTime('11:00'));
        $unavailabilityMock->method('getEndTime')->willReturn(new DateTime('13:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([$unavailabilityMock]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        $expected = [
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '15:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '17:00'],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetDayAvailabilityHandlesFullDayUnavailability(): void
    {
        $day           = new DateTime('2023-11-10');
        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('17:00'));

        $unavailabilityMock = $this->createMock(Unavailability::class);
        $unavailabilityMock->method('isFullDay')->willReturn(true);

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([$unavailabilityMock]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertSame([], $result);
    }
}
