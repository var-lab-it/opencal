<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Event;
use App\Message\EventCanceledMessage;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class CancelEventAsAttendeeTest extends ApiTestCase
{
    use InteractsWithMessenger;

    public function testCancelEventAsAttendee(): void
    {
        $client    = static::createClient();
        $container = $client->getContainer();

        /** @phpstan-ignore-next-line */
        $em    = $container->get('doctrine')->getManager();
        $repo  = $em->getRepository(Event::class);
        $event = $repo->find(1);

        self::assertInstanceOf(
            Event::class,
            $event,
        );

        $client->request('POST', '/events/1/cancel', [
            'headers' => [
                'Content-Type' => 'application/json',
                'accept'       => 'application/json',
            ],
            'json'    => [
                'cancellationHash' => $event->getCancellationHash(),
            ],
        ]);

        self::assertResponseIsSuccessful();

        $this->transport()->queue()->assertContains(EventCanceledMessage::class);
    }
}
