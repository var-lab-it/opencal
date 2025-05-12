<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailNotificationService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly ICalService $iCalService,
        private readonly Filesystem $filesystem,
        private readonly string $emailSenderAddress,
        private readonly string $emailSenderName,
        private readonly string $locale,
    ) {
    }

    public function sendNewBookingNotificationToHost(Event $event): void
    {
        $params = $this->getParams($event);

        $iCalTmpFilePath = $this->iCalService->exportEvent($event);

        $this->sendNotification(
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

    public function sendBookingConfirmationToAttendee(Event $event): void
    {
        $params = $this->getParams($event);

        $iCalTmpFilePath = $this->iCalService->exportEvent($event);

        $this->sendNotification(
            $this->translator->trans('mails.booking.new.to_attendee.subject', $params, 'messages', $this->locale),
            $this->translator->trans('mails.booking.new.to_attendee.message', $params, 'messages', $this->locale),
            $event->getParticipantEmail(),
            $event->getParticipantName(),
            [
                $iCalTmpFilePath => 'invite.ics',
            ],
        );

        $this->filesystem->remove($iCalTmpFilePath);
    }

    /** @return array<string, string|int> */
    private function getParams(Event $event): array
    {
        return [
            '{attendee_name}'   => $event->getParticipantName(),
            '{time_from}'       => $event->getStartTime()->format('H:i'),
            '{booking_date}'    => $event->getDay()->format('d.m.Y'),
            '{event_type_name}' => $event->getEventType()->getName(),
            '{duration}'        => $event->getEventType()->getDuration(),
            '{email_attendee}'  => $event->getParticipantEmail(),
            '{given_name}'      => $event->getEventType()->getHost()->getGivenName(),
            '{family_name}'     => $event->getEventType()->getHost()->getFamilyName(),
            '{host_email}'      => $event->getEventType()->getHost()->getEmail(),
        ];
    }

    /**
     * @param array<string, string> $attachments
     *
     * @throws TransportExceptionInterface
     */
    private function sendNotification(
        string $subject,
        string $message,
        string $recipientEmail,
        string $recipientName,
        array $attachments = [],
    ): void {
        $email = (new Email())
            ->from(new Address($this->emailSenderAddress, $this->emailSenderName))
            ->to(new Address($recipientEmail, $recipientName))
            ->subject($subject)
            ->text($message);

        foreach ($attachments as $filePath => $attachmentName) {
            $email->attachFromPath($filePath, $attachmentName);
        }

        $this->mailer->send($email);
    }
}
