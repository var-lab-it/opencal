<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends ApiTestCase
{
    public function testNotAuthenticated(): void
    {
        $client = static::createClient();

        $client->request('GET', '/me', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthWithDisabledUser(): void
    {
        $client    = self::createClient();
        $container = self::getContainer();

        /** @var EntityManager $em */
        $em = $container->get(EntityManagerInterface::class);

        $email    = 'disabled@test.tld';
        $password = 'not-required';
        $user     = new User();
        $user
            ->setGivenName('Test')
            ->setFamilyName('Disabled')
            ->setEnabled(false)
            ->setEmail($email)
            ->setPassword($password);
        $em->persist($user);
        $em->flush();
        $em->clear();

        $response = $client->request('POST', '/auth', [
            'headers' => [
                'Content-Type' => 'application/json',
                'accept'       => 'application/json',
            ],
            'json'    => [
                'email'    => $email,
                'password' => $password,
            ],
        ]);

        self::assertSame(
            $response->getStatusCode(),
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
