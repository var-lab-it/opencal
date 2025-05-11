<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Availability;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Availability> */
class AvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availability::class);
    }

    /** @return array<Availability> */
    public function findAllByWeekDayAndUser(string $weekDay, User $user): array
    {
        /** @var array<Availability> $result */
        $result = $this->findBy([
            'dayOfWeek' => $weekDay,
            'user'      => $user,
        ], [
            'startTime' => 'ASC',
        ]);

        return $result;
    }
}
