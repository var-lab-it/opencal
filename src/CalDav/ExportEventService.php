<?php

declare(strict_types=1);

namespace App\CalDav;

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
use function Safe\tempnam;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\fclose;

class ExportEventService
{
    public function __construct()
    {
    }

    public function exportEvent(Event $event): string
    {
        if (null !== $event->getSyncHash()) {
            throw new \RuntimeException('A synced event cannot be exported as .ics-file.');
        }

        /** @var EventType $eventType */
        $eventType = $event->getEventType();

        $iCalEvent = new iCalEvent();
        $iCalEvent
            ->setSummary($eventType->getName())
            ->setDescription($event->getParticipantMessage() ?? '')
            ->setOrganizer(new Organizer(
                new EmailAddress($eventType->getHost()->getEmail()),
                \sprintf(
                    '%s %s',
                    $eventType->getHost()->getGivenName(),
                    $eventType->getHost()->getFamilyName(),
                ),
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

        if (null !== $event->getParticipantEmail()) {
            $attendee = new Attendee(
                new EmailAddress($event->getParticipantEmail()),
            );

            if (null !== $event->getParticipantName()) {
                $attendee->setDisplayName($event->getParticipantName());
            }

            $iCalEvent
                ->addAttendee($attendee);
        }

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
