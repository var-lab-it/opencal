<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Entity\EventType;
use Sabre\VObject;
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

        $vCalendar = new VObject\Component\VCalendar([
            'VEVENT' => [
                'SUMMARY'   => $event->getEventType()->getName(),
                'DTSTAMP'   => $event->getDay(),
                'DTSTART'   => new DateTimeImmutable(
                    $event->getDay()->format('Y-m-d') .
                    ' ' . $event->getStartTime()->format('H:i:s'),
                ),
                'DTEND'     => new DateTimeImmutable(
                    $event->getDay()->format('Y-m-d') .
                    ' ' . $event->getEndTime()->format('H:i:s'),
                ),
                'ATTENDEE'  => 'mailto:' . $event->getParticipantEmail(),
                'ORGANIZER' => 'mailto:' . $event->getEventType()->getHost()->getEmail(),
            ],
        ]);

        /** @phpstan-ignore-next-line */
        $vCalendar->VEVENT->ATTENDEE['CN'] = $event->getParticipantName();
        /** @phpstan-ignore-next-line */
        $vCalendar->VEVENT->ORGANIZER['CN'] = $event->getEventType()->getHost()->getGivenName() . ' '
            . $event->getEventType()->getHost()->getFamilyName();

        $iCalContent = $vCalendar->serialize();

        $tmpFilePath = tempnam(\sys_get_temp_dir(), 'opencal_');
        $fHandle     = fopen($tmpFilePath, 'w');
        fwrite($fHandle, $iCalContent);
        fclose($fHandle);

        return $tmpFilePath;
    }
}
