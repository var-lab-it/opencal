<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\EventType;
use App\Repository\EventTypeRepository;
use App\Service\AvailabilityService;
use Safe\DateTime;

/** @phpstan-ignore-next-line */
class DayAvailabilityStateProvider implements ProviderInterface
{
    public function __construct(
        private readonly AvailabilityService $availabilityService,
        private readonly EventTypeRepository $eventTypeRepository,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var array{
         *     email: string,
         *     date: string,
         *     event_type_id: string,
        } $filters */
        $filters = $context['filters'] ?? [];

        $dayDT = new DateTime($filters['date']);

        $eventType = $this
            ->eventTypeRepository
            ->find(\intval($filters['event_type_id']));

        if (!$eventType instanceof EventType) {
            return [];
        }

        $availabilities = $this
            ->availabilityService
            ->getDayAvailability($dayDT, $eventType);

        /** @var array<string, array<string>|string> $result */
        $result = [
            'day_of_week'    => \strtolower($dayDT->format('l')),
            'event_type'     => [
                'id'   => $eventType->getId(),
                'name' => $eventType->getName(),
            ],
            'availabilities' => $availabilities,
        ];

        return $result; // @phpstan-ignore-line to return a array is also ok
    }
}
