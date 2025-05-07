<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
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

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetOneAsUser2NoAccess(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken(
            'jane.smith@example.tld',
        );

        $response = $client->request('GET', '/events/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            Response::HTTP_NOT_FOUND,
            $response->getStatusCode(),
        );
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
                'type'             => 'event_types/1',
                'name'             => 'Test Event',
                'description'      => 'Test Event Description',
                'startDateTime'    => '2022-01-01T12:00:00+00:00',
                'endDateTime'      => '2022-01-01T12:00:30+00:00',
                'participantName'  => 'Test User',
                'participantEmail' => 'mail@user.tld',
            ],
        ]);

        $json = $response->toArray();

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
