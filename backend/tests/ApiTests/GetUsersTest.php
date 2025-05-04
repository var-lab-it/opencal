<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GetUsersTest extends ApiTestCase
{
    use RetrieveTokenTrait;

    public function testGetUsers(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $client->request('GET', '/users', [
            'auth_bearer' => $token,
        ]);

        self::assertResponseIsSuccessful();
    }
}
