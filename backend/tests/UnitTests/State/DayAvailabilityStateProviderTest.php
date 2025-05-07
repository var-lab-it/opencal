<?php

declare(strict_types=1);

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\EventType;
use App\Entity\User;
use App\Repository\EventTypeRepository;
use App\Repository\UserRepository;
use App\Service\AvailabilityService;
use App\State\DayAvailabilityStateProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DayAvailabilityStateProviderTest extends TestCase
{
    private UserRepository&MockObject $userRepositoryMock;
    private EventTypeRepository&MockObject $eventTypeRepositoryMock;
    private AvailabilityService&MockObject $availabilityServiceMock;

    private DayAvailabilityStateProvider $provider;

    protected function setUp(): void
    {
        $this->userRepositoryMock      = $this->createMock(UserRepository::class);
        $this->eventTypeRepositoryMock = $this->createMock(EventTypeRepository::class);
        $this->availabilityServiceMock = $this->createMock(AvailabilityService::class);

        $this->provider = new DayAvailabilityStateProvider(
            $this->availabilityServiceMock,
            $this->userRepositoryMock,
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

        $this->userRepositoryMock
            ->method('findOneBy')
            ->with(['email' => $filters['email']])
            ->willReturn($user);

        $this->eventTypeRepositoryMock
            ->method('findOneByIdAndEmail')
            ->with(1, $filters['email'])
            ->willReturn($eventType);

        $this->availabilityServiceMock
            ->method('getDayAvailability')
            ->with(new DateTime('2023-11-22'), $user, $eventType)
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

    public function testProvideThrowsNotFoundHttpExceptionForMissingUser(): void
    {
        self::expectException(NotFoundHttpException::class);
        self::expectExceptionMessage('User not found.');

        $filters = [
            'email'         => 'missing@example.com',
            'date'          => '2023-11-22',
            'event_type_id' => '1',
        ];

        $this->userRepositoryMock
            ->method('findOneBy')
            ->with(['email' => $filters['email']])
            ->willReturn(null);

        $operation = $this->createMock(Operation::class);

        $this->provider->provide($operation, [], ['filters' => $filters]);
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

        $this->userRepositoryMock
            ->method('findOneBy')
            ->with(['email' => $filters['email']])
            ->willReturn($user);

        $this->eventTypeRepositoryMock
            ->method('findOneByIdAndEmail')
            ->with(999, $filters['email'])
            ->willReturn(null);

        $operation = $this->createMock(Operation::class);

        $result = $this->provider->provide($operation, [], ['filters' => $filters]);

        self::assertEmpty($result);
    }
}
