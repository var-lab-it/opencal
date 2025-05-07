<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\EventType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: EventType::class)]
class EventTypePrePersistEventListener
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function prePersist(EventType $eventType): void
    {
        $user = $this->security->getUser();

        if (!($user instanceof User)) {
            return;
        }

        $eventType->setHost($user);
    }
}
