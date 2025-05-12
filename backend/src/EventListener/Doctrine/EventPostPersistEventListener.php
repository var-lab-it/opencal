<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\Event;
use App\Message\NewBookingMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Event::class)]
class EventPostPersistEventListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function postPersist(Event $event): void
    {
        $this->messageBus->dispatch(
            new NewBookingMessage($event->getId()),
        );
    }
}
