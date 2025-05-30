<?php

declare(strict_types=1);

namespace App\Mail;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $emailSenderAddress,
        private readonly string $emailSenderName,
    ) {
    }

    /**
     * @param array<string, string> $attachments
     *
     * @throws TransportExceptionInterface
     */
    public function sendEmail(
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
