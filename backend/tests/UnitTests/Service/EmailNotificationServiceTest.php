<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Service;

use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\User;
use App\Service\EmailNotificationService;
use App\Service\ICalService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailNotificationServiceTest extends TestCase
{
    private MailerInterface&MockObject $mailerMock;
    private TranslatorInterface&MockObject $translatorMock;
    private ICalService&MockObject $iCalServiceMock;
    private Filesystem&MockObject $filesystemMock;
    private EmailNotificationService $emailNotificationService;

    private const EMAIL_SENDER_ADDRESS = 'no-reply@example.com';
    private const EMAIL_SENDER_NAME    = 'Example App';
    private const LOCALE               = 'en';

    protected function setUp(): void
    {
        $this->mailerMock      = $this->createMock(MailerInterface::class);
        $this->translatorMock  = $this->createMock(TranslatorInterface::class);
        $this->iCalServiceMock = $this->createMock(ICalService::class);
        $this->filesystemMock  = $this->createMock(Filesystem::class);

        $this->emailNotificationService = new EmailNotificationService(
            $this->mailerMock,
            $this->translatorMock,
            $this->iCalServiceMock,
            $this->filesystemMock,
            self::EMAIL_SENDER_ADDRESS,
            self::EMAIL_SENDER_NAME,
            self::LOCALE,
        );
    }

    public function testSendNewBookingNotificationToHost(): void
    {
        $event  = $this->createEventMock();
        $params = $this->getExpectedParams();

        $this->translatorMock
            ->method('trans')
            ->willReturnMap([
                ['mails.booking.new.to_host.subject', $params, 'messages', self::LOCALE, 'Subject for Host'],
                ['mails.booking.new.to_host.message', $params, 'messages', self::LOCALE, 'Message for Host'],
            ]);

        $this->iCalServiceMock
            ->method('exportEvent')
            ->with($event)
            ->willReturn('/path/to/temp.ics');

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            /** @phpstan-ignore-next-line */
            ->with($this->callback(static function (Email $email) {
                return 'Subject for Host' === $email->getSubject()
                    && 'Message for Host' === $email->getTextBody()
                    && 'invite.ics' === $email->getAttachments()[0]->getName();
            }));

        $this->filesystemMock
            ->expects($this->once())
            ->method('remove')
            ->with('/path/to/temp.ics');

        $this->emailNotificationService->sendNewBookingNotificationToHost($event);
    }

    public function testSendBookingConfirmationToAttendee(): void
    {
        $event  = $this->createEventMock();
        $params = $this->getExpectedParams();

        $this->translatorMock
            ->method('trans')
            ->willReturnMap([
                ['mails.booking.new.to_attendee.subject', $params, 'messages', self::LOCALE, 'Subject for Attendee'],
                ['mails.booking.new.to_attendee.message', $params, 'messages', self::LOCALE, 'Message for Attendee'],
            ]);

        $this->iCalServiceMock
            ->method('exportEvent')
            ->with($event)
            ->willReturn('/path/to/temp.ics');

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            /** @phpstan-ignore-next-line */
            ->with($this->callback(static function (Email $email) {
                return 'Subject for Attendee' === $email->getSubject()
                    && 'Message for Attendee' === $email->getTextBody()
                    && 'invite.ics' === $email->getAttachments()[0]->getName();
            }));

        $this->filesystemMock
            ->expects($this->once())
            ->method('remove')
            ->with('/path/to/temp.ics');

        $this->emailNotificationService->sendBookingConfirmationToAttendee($event);
    }

    /** @return array<string, string|int> */
    private function getExpectedParams(): array
    {
        return [
            '{attendee_name}'   => 'John Doe',
            '{time_from}'       => '14:00',
            '{booking_date}'    => '01.01.2024',
            '{event_type_name}' => 'Meeting',
            '{duration}'        => 60,
            '{email_attendee}'  => 'john.doe@example.com',
            '{given_name}'      => 'Host GivenName',
            '{family_name}'     => 'Host FamilyName',
            '{host_email}'      => 'host@example.com',
        ];
    }

    private function createEventMock(): Event
    {
        $host = $this->createMock(User::class);
        $host
            ->method('getEmail')
            ->willReturn('host@example.com');
        $host
            ->method('getGivenName')
            ->willReturn('Host GivenName');
        $host
            ->method('getFamilyName')
            ->willReturn('Host FamilyName');

        $eventType = $this->createMock(EventType::class);
        $eventType
            ->method('getHost')
            ->willReturn($host);
        $eventType
            ->method('getName')
            ->willReturn('Meeting');
        $eventType
            ->method('getDuration')
            ->willReturn(60);

        $event = $this->createMock(Event::class);
        $event
            ->method('getParticipantName')
            ->willReturn('John Doe');
        $event
            ->method('getParticipantEmail')
            ->willReturn('john.doe@example.com');
        $event
            ->method('getStartTime')
            ->willReturn(new DateTime('2024-01-01 14:00:00'));
        $event
            ->method('getDay')
            ->willReturn(new DateTime('2024-01-01'));
        $event
            ->method('getEventType')
            ->willReturn($eventType);

        return $event;
    }
}
