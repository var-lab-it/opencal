<?php

declare(strict_types=1);

namespace App\Notification\Email;

use App\Entity\Event;
use App\Mail\MailService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

abstract class AbstractEmailNotificationService
{
    public function __construct(
        private readonly MailService $mailService,
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
    protected function sendEmail(
        string $subject,
        string $message,
        string $recipientEmail,
        string $recipientName,
        array $attachments = [],
    ): void {
        $this->mailService->sendEmail($subject, $message, $recipientEmail, $recipientName, $attachments);
    }
}
