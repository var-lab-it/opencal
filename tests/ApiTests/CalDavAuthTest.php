<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Response;

class CalDavAuthTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetCalDavAuthsAsUser1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_auths', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetCalDavAuthsAsUser2(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken(
            'jane.smith@example.tld',
        );

        $response = $client->request('GET', '/cal_dav_auths', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetCalDavAuthsNotAuthenticated(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', '/cal_dav_auths', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            Response::HTTP_UNAUTHORIZED,
            $response->getStatusCode(),
        );
    }

    public function testGetOneCalDavAuthsAsUser1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_auths/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetOneCalDavAuthsWithWrongUser(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_auths/2', [
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

    public function testPost(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/cal_dav_auths', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'enabled'  => true,
                'baseUri'  => 'http://example.com',
                'username' => 'user',
                'password' => 'password',
            ],
        ]);

        self::assertSame(
            Response::HTTP_CREATED,
            $response->getStatusCode(),
        );

        $json = $response->toArray();
        self::assertIsInt($json['id']);
        unset($json['id']);
        self::assertMatchesJsonSnapshot($json);
    }

    public function testPatch(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/cal_dav_auths/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept'       => 'application/json',
                'content-type' => 'application/merge-patch+json',
            ],
            'json'        => [
                'enabled' => false,
            ],
        ]);

        self::assertSame(
            Response::HTTP_OK,
            $response->getStatusCode(),
        );

        $json = $response->toArray();
        self::assertMatchesJsonSnapshot($json);
    }

    public function testDelete(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/cal_dav_auths', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'enabled'  => true,
                'baseUri'  => 'http://example.com',
                'username' => 'user',
                'password' => 'password',
            ],
        ]);

        self::assertSame(
            Response::HTTP_CREATED,
            $response->getStatusCode(),
        );

        /** @var array{id: int} $json */
        $json = $response->toArray();

        $response = $client->request('DELETE', "/cal_dav_auths/{$json['id']}", [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode(),
        );

        $response = $client->request('GET', "/cal_dav_auths/{$json['id']}", [
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
}
