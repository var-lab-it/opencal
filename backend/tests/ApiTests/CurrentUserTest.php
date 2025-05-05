<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CurrentUserTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetUsers(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/me', [
            'auth_bearer' => $token,
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }
}
