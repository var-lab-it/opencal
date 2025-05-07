<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\EventType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user1 = $this->getReference('user1', User::class);
        $user2 = $this->getReference('user2', User::class);

        $eventTypesData = [
            [
                'name'        => 'Conference Call',
                'description' => 'A call to discuss project updates.',
                'duration'    => 60,
                'slug'        => 'conference-call',
                'host'        => $user1,
            ],
            [
                'name'        => 'Team Meeting',
                'description' => 'Weekly sync with the team.',
                'duration'    => 30,
                'slug'        => 'team-meeting',
                'host'        => $user2,
            ],
            [
                'name'        => 'One-on-One',
                'description' => 'Personal meeting with a team member.',
                'duration'    => 45,
                'slug'        => 'one-on-one',
                'host'        => $user1,
            ],
        ];

        foreach ($eventTypesData as $data) {
            $eventType = new EventType();
            $eventType->setName($data['name'])
                ->setDescription($data['description'])
                ->setDuration($data['duration'])
                ->setSlug($data['slug'])
                ->setHost($data['host']);

            $manager->persist($eventType);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
