<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<EventType> */
class EventTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventType::class);
    }

    public function findOneByIdAndEmail(int $id, string $email): ?EventType
    {
        /** @var ?EventType $result */
        $result = $this->createQueryBuilder('e')
           ->leftJoin('e.host', 'h')
           ->where('e.id = :id')
           ->andWhere('h.email = :email')
           ->setParameter('id', $id)
           ->setParameter('email', $email)
           ->getQuery()
           ->getOneOrNullResult();

        return $result;
    }
}
