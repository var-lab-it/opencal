<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Availability;
use App\Entity\EventType;
use App\Entity\Unavailability;
use App\Repository\AvailabilityRepository;
use App\Repository\UnavailabilityRepository;
use Safe\DateTime;
use function Safe\strtotime;

class AvailabilityService
{
    public const string MONDAY    = 'monday';
    public const string TUESDAY   = 'tuesday';
    public const string WEDNESDAY = 'wednesday';
    public const string THURSDAY  = 'thursday';
    public const string FRIDAY    = 'friday';
    public const string SATURDAY  = 'saturday';
    public const string SUNDAY    = 'sunday';

    public function __construct(
        private readonly UnavailabilityRepository $unavailabilityRepository,
        private readonly AvailabilityRepository $availabilityRepository,
    ) {
    }

    /** @return list<array<string, string>> */
    public function getDayAvailability(DateTime $day, EventType $eventType): array
    {
        $weekDay = $day->format('l');

        $unavailabilities = $this
            ->unavailabilityRepository
            ->findByWeekDayAndUser($weekDay, $eventType->getHost());

        $availabilities = $this
            ->availabilityRepository
            ->findAllByWeekDayAndUser($weekDay, $eventType->getHost());

        return $this->buildTimeSlots($availabilities, $eventType->getDuration(), $unavailabilities);
    }

    /**
     * @param array<Availability> $availabilities
     * @param array<Unavailability> $unavailabilities
     *
     * @return list<array{start: non-falsy-string, end: non-falsy-string}>
     *
     * @throws \Safe\Exceptions\DatetimeException
     */
    private function buildTimeSlots(array $availabilities, int $duration, array $unavailabilities): array
    {
        $slots = [];

        /** @var Availability $availability */
        foreach ($availabilities as $availability) {
            $startTimeString = $availability->getStartTime()->format('H:i');
            $endTimeString   = $availability->getEndTime()->format('H:i');

            $start = \explode(':', $startTimeString);

            $end = \explode(':', $endTimeString);

            $current = new DateTime();
            $current->setTime((int) $start[0], (int) $start[1]);

            $endTime = new DateTime();
            $endTime->setTime((int) $end[0], (int) $end[1]);

            while ($current < $endTime) {
                $nextSlot = clone $current;
                $nextSlot->modify("+{$duration} minutes");

                $slot = [
                    'start' => $current->format('H:i'),
                    'end'   => $nextSlot->format('H:i'),
                ];

                if ($nextSlot <= $endTime && true === !$this->isUnavailable($slot, $unavailabilities)) {
                    $slots[] = $slot;
                }

                $current = $nextSlot;
            }
        }

        return $slots;
    }

    /**
     * @param array{start: string, end: string} $slot
     * @param array<Unavailability> $unavailabilities
     */
    private function isUnavailable(array $slot, array $unavailabilities): bool
    {
        foreach ($unavailabilities as $unavailability) {
            if (true === $unavailability->isFullDay() || null === $unavailability->isFullDay()) {
                return true;
            }

            if (!$unavailability->getStartTime() instanceof DateTime
                || !$unavailability->getEndTime() instanceof DateTime
            ) {
                continue;
            }

            $startTimeString = $unavailability->getStartTime()->format('H:i');
            $endTimeString   = $unavailability->getEndTime()->format('H:i');

            $unavailableStart = strtotime($startTimeString);
            $unavailableEnd   = strtotime($endTimeString);

            $slotTimeStart = strtotime($slot['start']);
            $slotTimeEnd   = strtotime($slot['end']);

            if (($slotTimeStart < $unavailableEnd && $slotTimeEnd > $unavailableStart)
            ) {
                return true;
            }
        }

        return false;
    }
}
