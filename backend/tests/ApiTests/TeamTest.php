<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class TeamTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetTeams(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/teams', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetTeam1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/teams/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetTeam2(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $client->request('GET', '/teams/2', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testGetTeam3NotFound(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/teams/3', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(403, $response->getStatusCode());
    }
}
