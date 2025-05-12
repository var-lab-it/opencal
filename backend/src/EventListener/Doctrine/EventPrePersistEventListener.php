<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Event::class)]
class EventPrePersistEventListener
{
    public function prePersist(Event $event): void
    {
        $event->setCancellationHash(\md5(\uniqid('', true)));
    }
}
