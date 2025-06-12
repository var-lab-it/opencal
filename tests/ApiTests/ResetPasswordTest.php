<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Message\PasswordRequestedMessage;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class ResetPasswordTest extends ApiTestCase
{
    use InteractsWithMessenger;

    public function testRequestPasswordWithExistingUser(): void
    {
        self::transport()->reset();

        $client = static::createClient();

        $response = $client->request('POST', '/password/request', [
            'headers' => [
                'accept' => 'application/json',
            ],
            'json'    => [
                'email' => 'user@example.tld',
            ],
        ]);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::transport()->queue()->assertCount(1);
        self::transport()->queue()->assertContains(PasswordRequestedMessage::class);
    }

    public function testRequestPasswordWithUserNotFound(): void
    {
        self::transport()->reset();

        $client = static::createClient();

        $response = $client->request('POST', '/password/request', [
            'headers' => [
                'accept' => 'application/json',
            ],
            'json'    => [
                'email' => 'no-user@example.tld',
            ],
        ]);

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::transport()->queue()->assertCount(0);
    }
}
