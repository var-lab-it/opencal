<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Entity\EventType;
use Eluceo\iCal\Domain\Entity\Attendee;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event as iCalEvent;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Safe\DateTimeImmutable;
use function Safe\fclose;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\tempnam;

class ICalService
{
    public function __construct()
    {
    }

    public function exportEvent(Event $event): string
    {
        if (!$event->getEventType() instanceof EventType) {
            throw new \RuntimeException('Event has no event type');
        }

        if (null === $event->getParticipantEmail()) {
            throw new \RuntimeException('Event has no participant email');
        }

        $iCalEvent = new iCalEvent();
        $iCalEvent
            ->setSummary($event->getEventType()->getName())
            ->setDescription($event->getParticipantMessage() ?? '')
            ->setOrganizer(new Organizer(
                new EmailAddress($event->getEventType()->getHost()->getEmail()),
                \sprintf(
                    '%s %s',
                    $event->getEventType()->getHost()->getGivenName(),
                    $event->getEventType()->getHost()->getFamilyName(),
                ),
            ))
            ->addAttendee(new Attendee(
                new EmailAddress($event->getParticipantEmail()),
            ))
            ->setOccurrence(
                new TimeSpan(
                    new DateTime(new DateTimeImmutable(\sprintf(
                        '%s %s',
                        $event->getDay()->format('Y-m-d'),
                        $event->getStartTime()->format('H:i:s'),
                    )), true),
                    new DateTime(new DateTimeImmutable(\sprintf(
                        '%s %s',
                        $event->getDay()->format('Y-m-d'),
                        $event->getEndTime()->format('H:i:s'),
                    )), true),
                ),
            );

        $calendar = new Calendar([$iCalEvent]);

        $componentFactory = new CalendarFactory();

        $iCalContent = $componentFactory->createCalendar($calendar)->__toString();

        $tmpFilePath = tempnam(\sys_get_temp_dir(), 'opencal_');
        $fHandle     = fopen($tmpFilePath, 'w');
        fwrite($fHandle, $iCalContent);
        fclose($fHandle);

        return $tmpFilePath;
    }
}
