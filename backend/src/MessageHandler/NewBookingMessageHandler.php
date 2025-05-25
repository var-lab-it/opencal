<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Event;
use App\Message\NewBookingMessage;
use App\Notification\Email\NewBookingToAttendeeEmailNotificationService;
use App\Notification\Email\NewBookingToHostEmailNotificationService;
use App\Repository\EventRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NewBookingMessageHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly NewBookingToHostEmailNotificationService $newBookingToHostEmailNotificationService,
        private readonly NewBookingToAttendeeEmailNotificationService $newBookingToAttendeeEmailNotificationService,
    ) {
    }

    public function __invoke(NewBookingMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());

        if (!$event instanceof Event || null !== $event->getSyncHash()) {
            return;
        }

        $this->newBookingToHostEmailNotificationService->sendNotification($event);
        $this->newBookingToAttendeeEmailNotificationService->sendNotification($event);
    }
}
