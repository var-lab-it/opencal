<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends ApiTestCase
{
    public function testNotAuthenticated(): void
    {
        $client = static::createClient();

        $client->request('GET', '/me');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
