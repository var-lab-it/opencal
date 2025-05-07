<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Unavailability;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Unavailability> */
class UnavailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unavailability::class);
    }

    /** @return array<Unavailability> */
    public function findByWeekDayAndUser(string $weekDay, User $user): array
    {
        return $this->findBy([
            'dayOfWeek' => $weekDay,
            'user'      => $user,
        ]);
    }
}
