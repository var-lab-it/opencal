<?php

declare(strict_types=1);

namespace App\Notification\Email;

use App\Entity\Event;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

abstract class AbstractEmailNotificationService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $emailSenderAddress,
        private readonly string $emailSenderName,
        private readonly string $frontendDomain,
        private readonly bool $useSSL,
    ) {
    }

    abstract public function sendNotification(Event $event): void;

    protected function getFrontendUrl(): string
    {
        $protocol = $this->useSSL ? 'https' : 'http';

        return \sprintf(
            '%s://%s',
            $protocol,
            $this->frontendDomain,
        );
    }

    /**
     * @param array<string, string> $attachments
     *
     * @throws TransportExceptionInterface
     */
    protected function sentEmail(
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
