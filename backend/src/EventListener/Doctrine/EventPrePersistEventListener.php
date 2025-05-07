<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Event::class)]
class EventPrePersistEventListener
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function prePersist(Event $eventType): void
    {
        $user = $this->security->getUser();

        if (!($user instanceof User)) {
            return;
        }

        $eventType->setHost($user);
    }
}
