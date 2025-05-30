<?php

declare(strict_types=1);

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use App\Availability\AvailabilityService;
use App\Entity\EventType;
use App\Entity\User;
use App\Repository\EventTypeRepository;
use App\State\DayAvailabilityStateProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class DayAvailabilityStateProviderTest extends TestCase
{
    private EventTypeRepository&MockObject $eventTypeRepositoryMock;
    private AvailabilityService&MockObject $availabilityServiceMock;

    private DayAvailabilityStateProvider $provider;

    protected function setUp(): void
    {
        $this->eventTypeRepositoryMock = $this->createMock(EventTypeRepository::class);
        $this->availabilityServiceMock = $this->createMock(AvailabilityService::class);

        $this->provider = new DayAvailabilityStateProvider(
            $this->availabilityServiceMock,
            $this->eventTypeRepositoryMock,
        );
    }

    public function testProvideReturnsDayAvailability(): void
    {
        $filters = [
            'email'         => 'test@example.com',
            'date'          => '2023-11-22',
            'event_type_id' => '1',
        ];

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($filters['email']);

        $eventType = $this->createMock(EventType::class);
        $eventType->method('getId')->willReturn(1);
        $eventType->method('getName')->willReturn('Conference Call');

        $availabilities = [['start' => '09:00', 'end' => '10:00']];

        $this->eventTypeRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($eventType);

        $this->availabilityServiceMock
            ->method('getDayAvailability')
            ->with(new DateTime('2023-11-22'), $eventType)
            ->willReturn($availabilities);

        $operation = $this->createMock(Operation::class);

        $result = $this->provider->provide($operation, [], ['filters' => $filters]);

        self::assertEquals([
            'day_of_week'    => 'wednesday',
            'event_type'     => [
                'id'   => 1,
                'name' => 'Conference Call',
            ],
            'availabilities' => $availabilities,
        ], $result);
    }

    public function testProvideReturnsEmptyArrayForMissingEventType(): void
    {
        $filters = [
            'email'         => 'test@example.com',
            'date'          => '2023-11-22',
            'event_type_id' => '999',
        ];

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($filters['email']);

        $this->eventTypeRepositoryMock
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $operation = $this->createMock(Operation::class);

        $result = $this->provider->provide($operation, [], ['filters' => $filters]);

        self::assertEmpty($result);
    }
}
