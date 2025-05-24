<?php

declare(strict_types=1);

namespace App\Service\Notification\Email;

use App\Entity\Event;
use App\Entity\EventType;
use App\Service\ICalExportService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewBookingToHostEmailNotificationService extends AbstractEmailNotificationService
{
    public function __construct(
        MailerInterface $mailer,
        string $emailSenderAddress,
        string $emailSenderName,
        string $frontendDomain,
        bool $useSSL,
        private readonly TranslatorInterface $translator,
        private readonly ICalExportService $iCalService,
        private readonly Filesystem $filesystem,
        private readonly string $locale,
    ) {
        parent::__construct(
            $mailer,
            $emailSenderAddress,
            $emailSenderName,
            $frontendDomain,
            $useSSL,
        );
    }

    public function sendNotification(Event $event): void
    {
        if (!$event->getEventType() instanceof EventType) {
            throw new \RuntimeException('Event has no event type');
        }

        $params = $this->getParams($event);

        $iCalTmpFilePath = $this->iCalService->exportEvent($event);

        $this->sentEmail(
            $this->translator->trans('mails.booking.new.to_host.subject', $params, 'messages', $this->locale),
            $this->translator->trans('mails.booking.new.to_host.message', $params, 'messages', $this->locale),
            $event->getEventType()->getHost()->getEmail(),
            \sprintf(
                '%s %s',
                $event->getEventType()->getHost()->getGivenName(),
                $event->getEventType()->getHost()->getFamilyName(),
            ),
            [
                $iCalTmpFilePath => 'invite.ics',
            ],
        );

        $this->filesystem->remove($iCalTmpFilePath);
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
            '{given_name}'      => $event->getEventType()->getHost()->getGivenName(),
            '{family_name}'     => $event->getEventType()->getHost()->getFamilyName(),
            '{frontend_url}'    => $this->getFrontendUrl(),
        ];
    }
}
