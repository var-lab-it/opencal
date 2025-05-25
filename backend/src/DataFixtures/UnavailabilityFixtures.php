<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Availability\AvailabilityService;
use App\Entity\Unavailability;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Safe\DateTime;

class UnavailabilityFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = $this->getReference('user1', User::class);

        $unavailableDays = [
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::TUESDAY,
                'start_time'  => '13:00',
                'end_time'    => '14:30',
            ],
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::SATURDAY,
                'full_day'    => true,
            ],
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::SUNDAY,
                'full_day'    => true,
            ],
        ];

        foreach ($unavailableDays as $dayData) {
            $unavailability = new Unavailability();
            $unavailability
                ->setUser($dayData['user'])
                ->setDayOfWeek($dayData['day_of_week'])
                ->setStartTime(isset($dayData['start_time']) ? new DateTime($dayData['start_time']) : null)
                ->setEndTime(isset($dayData['end_time']) ? new DateTime($dayData['end_time']) : null)
                ->setFullDay($dayData['full_day'] ?? false);

            $manager->persist($unavailability);
        }

        $manager->flush();
    }
}
