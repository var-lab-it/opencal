<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Team> */
class OrgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findOneByUser(int $id, User $user): ?Team
    {
        /** @var ?Team $result */
        $result = $this
            ->createQueryBuilder('o')
            ->where('o.id = :o_id')
            ->leftJoin('o.members', 'm')
            ->andWhere('m.id = :m_id')
            ->setParameter('o_id', $id)
            ->setParameter('m_id', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }
}
