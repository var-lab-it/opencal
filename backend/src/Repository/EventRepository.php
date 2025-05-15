<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Event> */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /** @return array<Event> */
    public function findAllByDayByUser(User $user, \DateTimeInterface $day): array
    {
        /** @var array<Event> $result */
        $result = $this->createQueryBuilder('event')
            ->leftJoin('event.eventType', 'event_type')
            ->leftJoin('event_type.host', 'user')
            ->where('user.id = :user_id')
            ->andWhere('event.day = :today')
            ->setParameter('user_id', $user->getId())
            ->setParameter('today', $day)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
