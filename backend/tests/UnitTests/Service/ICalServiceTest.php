<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Service;

use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\User;
use App\Service\ICalService;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Spatie\Snapshots\MatchesSnapshots;
use function Safe\file_get_contents;
use function Safe\unlink;

class ICalServiceTest extends TestCase
{
    use MatchesSnapshots;

    public function testExportEvent(): void
    {
        $event = new Event();
        $event
            ->setParticipantEmail('email@someone.tld')
            ->setParticipantName('Someone')
            ->setDay(new DateTime('2021-01-01'))
            ->setStartTime(new DateTime('10:00:00'))
            ->setEndTime(new DateTime('12:00:00'))
            ->setEventType(
                (new EventType())
                    ->setName('Test')
                    ->setDuration(30)
                    ->setHost(
                        (new User())
                            ->setEmail('test@unit.tld')
                            ->setGivenName('Test')
                            ->setFamilyName('User'),
                    ),
            );

        $service = new ICalService();
        $result  = $service->exportEvent($event);

        $iCalContent = file_get_contents($result);

        self::assertStringContainsString(
            'DTSTART:20210101T100000Z',
            $iCalContent,
        );
        self::assertStringContainsString(
            'DTEND:20210101T120000Z',
            $iCalContent,
        );
        self::assertStringContainsString(
            'VERSION:2.0',
            $iCalContent,
        );
        self::assertStringContainsString(
            'SUMMARY:Test',
            $iCalContent,
        );
        self::assertStringContainsString(
            'ORGANIZER;CN=Test User:mailto:test@unit.tld',
            $iCalContent,
        );
        self::assertStringContainsString(
            'ATTENDEE;CN=Someone:mailto:email@someone.tld',
            $iCalContent,
        );

        unlink($result);
    }
}
