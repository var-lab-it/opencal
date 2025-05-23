<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Event;
use App\Message\EventCanceledMessage;
use App\Repository\EventRepository;
use App\Service\Notification\Email\BookingCanceledToHostEmailNotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class EventCanceledMessageHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly BookingCanceledToHostEmailNotificationService $bookingCanceledToHostEmailNotificationService,
    ) {
    }

    public function __invoke(EventCanceledMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());

        if (!$event instanceof Event) {
            return;
        }

        $this->bookingCanceledToHostEmailNotificationService->sendNotification($event);
    }
}
