<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Availability;
use App\Entity\User;
use App\Service\AvailabilityService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Safe\DateTime;

class AvailabilityFixtures extends Fixture implements DependentFixtureInterface
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

        $data = [
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::MONDAY,
                'start_time'  => '09:00',
                'end_time'    => '12:00',
            ],
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::MONDAY,
                'start_time'  => '15:00',
                'end_time'    => '17:00',
            ],
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::TUESDAY,
                'start_time'  => '09:00',
                'end_time'    => '17:00',
            ],
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::WEDNESDAY,
                'start_time'  => '09:00',
                'end_time'    => '17:00',
            ],
            [
                'user'        => $user1,
                'day_of_week' => AvailabilityService::THURSDAY,
                'start_time'  => '09:00',
                'end_time'    => '17:00',
            ],
        ];

        foreach ($data as $dayData) {
            $availability = new Availability();
            $availability
                ->setUser($dayData['user'])
                ->setDayOfWeek($dayData['day_of_week'])
                ->setStartTime(new DateTime($dayData['start_time']))
                ->setEndTime(new DateTime($dayData['end_time']));

            $manager->persist($availability);
        }

        $manager->flush();
    }
}
