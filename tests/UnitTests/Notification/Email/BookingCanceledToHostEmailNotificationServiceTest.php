<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Notification\Email;

use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\User;
use App\Mail\MailService;
use App\Notification\Email\BookingCanceledToHostEmailNotificationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookingCanceledToHostEmailNotificationServiceTest extends TestCase
{
    use MatchesSnapshots;

    private MailService&MockObject $mailService;
    private TranslatorInterface&MockObject $translatorMock;

    protected function setUp(): void
    {
        $this->mailService    = $this->createMock(MailService::class);
        $this->translatorMock = $this->createMock(TranslatorInterface::class);
    }

    public function testSendNewBookingNotificationToAttendeeNoEventType(): void
    {
        $eventMock = $this->buildEventMock(false);

        $service = $this->getService();

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Event has no event type');

        $service->sendNotification($eventMock);
    }

    public function testSendNewBookingNotificationToAttendeeNoEmail(): void
    {
        $eventMock = $this->buildEventMock(true, false);

        $service = $this->getService();

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Event has no participant email');

        $service->sendNotification($eventMock);
    }

    public function testSendNewBookingNotificationToAttendeeSucceeds(): void
    {
        $this->mailService
            ->expects(self::once())
            ->method('sendEmail');

        $eventMock = $this->buildEventMock();

        $service = $this->getService();

        $service->sendNotification($eventMock);
    }

    public function testGetParamsWithoutEventType(): void
    {
        $service = $this->getService();

        $refClass = new \ReflectionClass($service);
        $method   = $refClass->getMethod('getParams');

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Event has no event type');

        $method->invokeArgs($service, [
            'event' => $this->buildEventMock(false),
        ]);
    }

    public function testGetParamsMatch(): void
    {
        $service = $this->getService();

        $refClass = new \ReflectionClass($service);
        $method   = $refClass->getMethod('getParams');

        /** @var array<array-key, mixed> $result */
        $result = $method->invokeArgs($service, [
            'event' => $this->buildEventMock(true),
        ]);

        self::assertMatchesJsonSnapshot($result);
    }

    private function buildEventMock(bool $withEventType = true, bool $withParticipantEmail = true): Event&MockObject
    {
        $hostMock = $this->createMock(User::class);
        $hostMock
            ->method('getEmail')
            ->willReturn('host@unit-test.tld');
        $hostMock
            ->method('getGivenName')
            ->willReturn('Unit');
        $hostMock
            ->method('getFamilyName')
            ->willReturn('Test');

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock
            ->method('getHost')
            ->willReturn($hostMock);
        $eventTypeMock
            ->method('getName')
            ->willReturn('Unit Test Event');
        $eventTypeMock
            ->method('getDuration')
            ->willReturn(30);

        $eventMock = $this->createMock(Event::class);

        if ($withEventType) {
            $eventMock
                ->method('getEventType')
                ->willReturn($eventTypeMock);
        }

        $eventMock
            ->method('getStartTime')
            ->willReturn(new DateTime('2025-04-02 10:00:00'));
        $eventMock
            ->method('getDay')
            ->willReturn(new DateTime('2025-04-02 00:00:00'));

        if ($withParticipantEmail) {
            $eventMock
                ->method('getParticipantEmail')
                ->willReturn('participant@unit-test.com');
        }

        $eventMock
            ->method('getId')
            ->willReturn(123);
        $eventMock
            ->method('getCancellationHash')
            ->willReturn('2e2af3da3b4d75fce835dad5ea077471');
        $eventMock
            ->method('getParticipantName')
            ->willReturn('The Attendee');

        return $eventMock;
    }

    private function getService(): BookingCanceledToHostEmailNotificationService
    {
        return new BookingCanceledToHostEmailNotificationService(
            $this->mailService,
            'http://frontend.tld',
            false,
            $this->translatorMock,
            'en_GB',
        );
    }
}
