<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\EventType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Safe\DateTime;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $eventType1 = $this->getReference('eventType1', EventType::class);
        $eventType2 = $this->getReference('eventType2', EventType::class);

        $eventsData = [
            [
                'type'               => $eventType1,
                'day'                => '2024-01-01',
                'startTime'          => '10:00',
                'endTime'            => '11:00',
                'eventTye'           => $eventType1,
                'participantName'    => 'John Doe',
                'participantEmail'   => 'john.doe@example.com',
                'participantMessage' => 'Looking forward to the event!',
            ],
            [
                'type'               => $eventType2,
                'day'                => '2024-01-01',
                'startTime'          => '10:00',
                'endTime'            => '10:30',
                'participantName'    => 'Jane Smith',
                'participantEmail'   => 'jane.smith@example.com',
                'participantMessage' => 'Can we discuss the agenda beforehand?',
            ],
            [
                'type'               => $eventType1,
                'day'                => '2024-01-01',
                'startTime'          => '10:00',
                'endTime'            => '11:00',
                'participantName'    => 'Alice Johnson',
                'participantEmail'   => 'alice.johnson@example.com',
                'participantMessage' => null,
            ],
        ];

        foreach ($eventsData as $data) {
            $event = new Event();
            $event
                ->setEventType($data['type'])
                ->setStartTime(new DateTime($data['startTime']))
                ->setEndTime(new DateTime($data['endTime']))
                ->setDay(new DateTime($data['day']))
                ->setParticipantName($data['participantName'])
                ->setParticipantEmail($data['participantEmail'])
                ->setParticipantMessage($data['participantMessage'] ?? null);

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
