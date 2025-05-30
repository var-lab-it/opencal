<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Response;

class EventTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetEventsAsUser1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/events', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        foreach ($json as $key => $value) {
            /** @phpstan-ignore-next-line */
            unset($json[$key]['cancellationHash']);
        }

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetEventsAsUser2(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken(
            'jane.smith@example.tld',
        );

        $response = $client->request('GET', '/events', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        foreach ($json as $key => $value) {
            /** @phpstan-ignore-next-line */
            unset($json[$key]['cancellationHash']);
        }

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetOneAsUser1Succeeds(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/events/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();
        unset($json['cancellationHash']);

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testCreateEventSucceeds(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/events', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'eventType'        => 'event_types/1',
                'name'             => 'Test Event',
                'description'      => 'Test Event Description',
                'day'              => '2024-03-05',
                'startTime'        => '11:00',
                'endTime'          => '11:30',
                'participantName'  => 'Test User',
                'participantEmail' => 'mail@user.tld',
            ],
        ]);

        $json = $response->toArray();
        unset($json['cancellationHash']);

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testPatchEvent(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/events/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept'       => 'application/json',
                'content-type' => 'application/merge-patch+json',
            ],
            'json'        => [
                'name'        => 'Test Event Type edited',
                'description' => 'Test Event Type edited Description',
            ],
        ]);

        $json = $response->toArray();
        unset($json['cancellationHash']);

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testDeleteEvent(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('DELETE', '/events/3', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode(),
        );
    }
}
