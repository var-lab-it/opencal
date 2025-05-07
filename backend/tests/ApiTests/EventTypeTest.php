<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

class EventTypeTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetEventTypesAsUser1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/event_types', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetEventTypesAsUser2(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken(
            'jane.smith@example.tld',
        );

        $response = $client->request('GET', '/event_types', [
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

        $response = $client->request('GET', '/event_types/1', [
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

        $response = $client->request('GET', '/event_types/1', [
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

    public function testCreateEventType(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/event_types', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'name'        => 'Test Event Type',
                'description' => 'Test Event Type Description',
                'duration'    => 30,
                'slug'        => 'test-event-type',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testCreateWithInvalidSlug(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/event_types', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'name'        => 'Test Event Type',
                'description' => 'Test Event Type Description',
                'duration'    => 30,
                'slug'        => 'aBsd )(ua9s8d?Adß)(A#ASd+',
            ],
        ]);

        self::expectException(ClientException::class);
        self::expectExceptionMessage('slug: The slug can only contain lowercase letters, numbers and dashes.');

        $response->toArray();
    }

    public function testPatchEventType(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/event_types/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept'       => 'application/json',
                'content-type' => 'application/merge-patch+json',
            ],
            'json'        => [
                'name'        => 'Test Event Type edited',
                'description' => 'Test Event Type edited Description',
                'duration'    => 60,
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testDeleteEventType(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('DELETE', '/event_types/3', [
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
