<?php

declare(strict_types=1);

namespace App\Notification\Email;

use App\Entity\Event;
use App\Entity\EventType;
use App\Mail\MailService;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookingCanceledToHostEmailNotificationService extends AbstractEmailNotificationService
{
    public function __construct(
        MailService $mailService,
        string $frontendDomain,
        bool $useSSL,
        private readonly TranslatorInterface $translator,
        private readonly string $locale,
    ) {
        parent::__construct(
            $mailService,
            $frontendDomain,
            $useSSL,
        );
    }

    public function sendNotification(Event $event): void
    {
        if (!$event->getEventType() instanceof EventType) {
            throw new \RuntimeException('Event has no event type');
        }

        if (null === $event->getParticipantEmail()) {
            throw new \RuntimeException('Event has no participant email');
        }

        $params = $this->getParams($event);

        $this->sendEmail(
            $this->translator->trans('mails.booking.cancellation.to_host.subject', $params, 'messages', $this->locale),
            $this->translator->trans('mails.booking.cancellation.to_host.message', $params, 'messages', $this->locale),
            $event->getParticipantEmail(),
            $event->getParticipantName() ?? 'unknown',
        );
    }

    /** @return array<string, string|int> */
    private function getParams(Event $event): array
    {
        if (!$event->getEventType() instanceof EventType) {
            throw new \RuntimeException('Event has no event type');
        }

        $unknownAttendee = $this->translator->trans('unknown_attendee', [], 'messages', $this->locale);
        $unknownEmail    = $this->translator->trans('unknown_email', [], 'messages', $this->locale);

        return [
            '{attendee_name}'   => $event->getParticipantName() ?? $unknownAttendee,
            '{time_from}'       => $event->getStartTime()->format('H:i'),
            '{booking_date}'    => $event->getDay()->format('d.m.Y'),
            '{event_type_name}' => $event->getEventType()->getName(),
            '{duration}'        => $event->getEventType()->getDuration(),
            '{email_attendee}'  => $event->getParticipantEmail() ?? $unknownEmail,
            '{host_email}'      => $event->getEventType()->getHost()->getEmail(),
            '{frontend_url}'    => $this->getFrontendUrl(),
        ];
    }
}
