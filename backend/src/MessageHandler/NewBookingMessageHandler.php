<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Event;
use App\Message\NewBookingMessage;
use App\Repository\EventRepository;
use App\Service\EmailNotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NewBookingMessageHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EmailNotificationService $notificationService,
    ) {
    }

    public function __invoke(NewBookingMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());

        if (!$event instanceof Event || null !== $event->getSyncHash()) {
            return;
        }

        $this->notificationService->sendNewBookingNotificationToHost($event);
        $this->notificationService->sendBookingConfirmationToAttendee($event);
    }
}
