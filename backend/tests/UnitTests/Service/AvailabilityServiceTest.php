<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Availability;
use App\Entity\EventType;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use App\Repository\UnavailabilityRepository;
use App\Service\AvailabilityService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class AvailabilityServiceTest extends TestCase
{
    private AvailabilityRepository&MockObject $availabilityRepositoryMock;
    private UnavailabilityRepository&MockObject $unavailabilityRepositoryMock;

    private AvailabilityService $availabilityService;

    protected function setUp(): void
    {
        $this->availabilityRepositoryMock   = $this->createMock(AvailabilityRepository::class);
        $this->unavailabilityRepositoryMock = $this->createMock(UnavailabilityRepository::class);
        $this->availabilityService          = new AvailabilityService(
            $this->unavailabilityRepositoryMock,
            $this->availabilityRepositoryMock,
        );
    }

    public function testGetDayAvailabilityReturnsCorrectTimeSlots(): void
    {
        $day       = new DateTime('2023-11-10'); // Friday
        $user      = new User();
        $eventType = $this->createMock(EventType::class);
        $eventType->method('getDuration')->willReturn(60);

        $availability = $this->createMock(Availability::class);
        $availability->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availability->method('getEndTime')->willReturn(new DateTime('17:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday', $user)
            ->willReturn([$availability]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday', $user)
            ->willReturn([]);

        $result = $this->availabilityService->getDayAvailability($day, $user, $eventType);

        $expected = [
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '11:00', 'end' => '12:00'],
            ['start' => '12:00', 'end' => '13:00'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '15:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '17:00'],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetDayAvailabilityRespectsUnavailabilities(): void
    {
        $day       = new DateTime('2023-11-10'); // Friday
        $user      = new User();
        $eventType = $this->createMock(EventType::class);
        $eventType->method('getDuration')->willReturn(60);

        $availability = $this->createMock(Availability::class);
        $availability->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availability->method('getEndTime')->willReturn(new DateTime('17:00'));

        $unavailability = $this->createMock(Unavailability::class);
        $unavailability->method('isFullDay')->willReturn(false);
        $unavailability->method('getStartTime')->willReturn(new DateTime('11:00'));
        $unavailability->method('getEndTime')->willReturn(new DateTime('13:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday', $user)
            ->willReturn([$availability]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday', $user)
            ->willReturn([$unavailability]);

        $result = $this->availabilityService->getDayAvailability($day, $user, $eventType);

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

    public function testGetDayAvailabilityDoesNotAddSlotsForFullDayUnavailability(): void
    {
        $day       = new DateTime('2023-11-10'); // Friday
        $user      = new User();
        $eventType = $this->createMock(EventType::class);
        $eventType->method('getDuration')->willReturn(60);

        $availability = $this->createMock(Availability::class);
        $availability->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availability->method('getEndTime')->willReturn(new DateTime('17:00'));

        $unavailability = $this->createMock(Unavailability::class);
        $unavailability->method('isFullDay')->willReturn(true);

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday', $user)
            ->willReturn([$availability]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday', $user)
            ->willReturn([$unavailability]);

        $result = $this->availabilityService->getDayAvailability($day, $user, $eventType);

        self::assertSame([], $result);
    }
}
