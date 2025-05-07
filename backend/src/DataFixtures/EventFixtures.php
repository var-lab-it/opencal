<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Safe\DateTime;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user1      = $this->getReference('user1', User::class);
        $user2      = $this->getReference('user2', User::class);
        $eventType1 = $this->getReference('eventType1', EventType::class);
        $eventType2 = $this->getReference('eventType2', EventType::class);

        $eventsData = [
            [
                'type'               => $eventType1,
                'startDateTime'      => new DateTime('2024-01-01 10:00:00'),
                'endDateTime'        => new DateTime('2024-01-01 11:00:00'),
                'participantName'    => 'John Doe',
                'participantEmail'   => 'john.doe@example.com',
                'participantMessage' => 'Looking forward to the event!',
                'host'               => $user1,
            ],
            [
                'type'               => $eventType2,
                'startDateTime'      => new DateTime('2024-01-02 14:00:00'),
                'endDateTime'        => new DateTime('2024-01-02 15:30:00'),
                'participantName'    => 'Jane Smith',
                'participantEmail'   => 'jane.smith@example.com',
                'participantMessage' => 'Can we discuss the agenda beforehand?',
                'host'               => $user2,
            ],
            [
                'type'               => $eventType1,
                'startDateTime'      => new DateTime('2024-01-03 16:00:00'),
                'endDateTime'        => new DateTime('2024-01-03 17:00:00'),
                'participantName'    => 'Alice Johnson',
                'participantEmail'   => 'alice.johnson@example.com',
                'participantMessage' => null,
                'host'               => $user1,
            ],
        ];

        foreach ($eventsData as $data) {
            $event = new Event();
            $event->setType($data['type'])
                ->setStartDateTime($data['startDateTime'])
                ->setEndDateTime($data['endDateTime'])
                ->setParticipantName($data['participantName'])
                ->setParticipantEmail($data['participantEmail'])
                ->setParticipantMessage($data['participantMessage'] ?? null)
                ->setHost($data['host']);

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            EventTypeFixtures::class,
        ];
    }
}
