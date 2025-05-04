<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

trait RetrieveTokenTrait
{
    public function retrieveToken(
        string $email = 'user@example.tld',
        string $password = 'password',
    ): string {
        $client = self::createClient();

        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json'    => [
                'email'    => $email,
                'password' => $password,
            ],
        ]);

        $json = $response->toArray();
        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $json);

        $token = $json['token'];

        self::assertIsString($token);

        return $token;
    }
}
