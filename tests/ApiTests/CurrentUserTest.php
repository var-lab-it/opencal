<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpClient\Exception\ClientException;

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

    public function testPatchUser(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/me/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'Content-Type' => 'application/merge-patch+json',
                'accept'       => 'application/json',
            ],
            'json'        => [
                'email'      => 'test@mail.tld',
                'givenName'  => 'Test',
                'familyName' => 'User',
            ],
        ]);

        $result = $response->toArray();
        self::assertMatchesJsonSnapshot($result);
        self::assertResponseIsSuccessful();
    }

    public function testPatchUserInvalidEmail(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/me/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'Content-Type' => 'application/merge-patch+json',
                'accept'       => 'application/json',
            ],
            'json'        => [
                'email'      => 'test',
                'givenName'  => 'Test',
                'familyName' => 'User',
            ],
        ]);

        self::expectException(ClientException::class);
        self::expectExceptionMessage('email: This value is not a valid email address.');
        $response->toArray();
    }

    public function testPatchUserGivenNameBlank(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/me/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'Content-Type' => 'application/merge-patch+json',
                'accept'       => 'application/json',
            ],
            'json'        => [
                'email'      => 'test@tld.com',
                'givenName'  => '',
                'familyName' => 'User',
            ],
        ]);

        self::expectException(ClientException::class);
        self::expectExceptionMessage('givenName: This value should not be blank.');
        $response->toArray();
    }

    public function testPatchUserFamilyNameBlank(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/me/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'Content-Type' => 'application/merge-patch+json',
                'accept'       => 'application/json',
            ],
            'json'        => [
                'email'      => 'test@tld.com',
                'givenName'  => 'User',
                'familyName' => '',
            ],
        ]);

        self::expectException(ClientException::class);
        self::expectExceptionMessage('familyName: This value should not be blank.');
        $response->toArray();
    }
}
