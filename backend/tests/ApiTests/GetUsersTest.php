<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GetUsersTest extends ApiTestCase
{
    public function testGetUsers(): void
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        self::assertResponseIsSuccessful();
    }
}
