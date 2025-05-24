<?php

declare(strict_types=1);

namespace App\Service\Notification\Email;

use App\Entity\Event;
use App\Entity\EventType;
use App\Service\ICalExportService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewBookingToAttendeeEmailNotificationService extends AbstractEmailNotificationService
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

        if (null === $event->getParticipantEmail() || '' === $event->getParticipantEmail()) {
            throw new \RuntimeException('Event has no participant email');
        }

        $params = $this->getParams($event);

        $iCalTmpFilePath = $this->iCalService->exportEvent($event);

        $this->sentEmail(
            $this->translator->trans('mails.booking.new.to_attendee.subject', $params, 'messages', $this->locale),
            $this->translator->trans('mails.booking.new.to_attendee.message', $params, 'messages', $this->locale),
            $event->getParticipantEmail(),
            $event->getParticipantName() ?? 'unknown',
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

        return [
            '{attendee_name}'    => $event->getParticipantName() ?? 'unknown',
            '{time_from}'        => $event->getStartTime()->format('H:i'),
            '{booking_date}'     => $event->getDay()->format('d.m.Y'),
            '{event_type_name}'  => $event->getEventType()->getName(),
            '{duration}'         => $event->getEventType()->getDuration(),
            '{email_attendee}'   => $event->getParticipantEmail() ?? 'not set', // todo: Should be a translatable string
            '{given_name}'       => $event->getEventType()->getHost()->getGivenName(),
            '{family_name}'      => $event->getEventType()->getHost()->getFamilyName(),
            '{host_email}'       => $event->getEventType()->getHost()->getEmail(),
            '{frontend_url}'     => $this->getFrontendUrl(),
            '{cancellation_url}' => \sprintf(
                '%s/event/%s/cancel/%s',
                $this->getFrontendUrl(),
                $event->getId(),
                $event->getCancellationHash(),
            ),
        ];
    }
}
