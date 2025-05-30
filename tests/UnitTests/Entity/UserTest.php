<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\Availability;
use App\Entity\CalDavAuth;
use App\Entity\EventType;
use App\Entity\Unavailability;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class UserTest extends TestCase
{
    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail('test@unit.tld');

        self::assertSame(
            'test@unit.tld',
            $user->getEmail(),
        );
    }

    public function testAddRole(): void
    {
        $user = new User();

        self::assertSame(
            [
                User::ROLE_USER,
            ],
            $user->getRoles(),
        );

        $user->addRole(User::ROLE_ADMIN);

        self::assertSame(
            [
                User::ROLE_ADMIN,
                User::ROLE_USER,
            ],
            $user->getRoles(),
        );
    }

    public function testId(): void
    {
        $user     = new User();
        $refClass = new \ReflectionClass($user);
        $prop     = $refClass->getProperty('id');
        $prop->setValue($user, 123);

        self::assertSame(
            123,
            $user->getId(),
        );
    }

    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('very!secure!!!11');

        self::assertSame(
            'very!secure!!!11',
            $user->getPassword(),
        );
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('email@test.com');

        self::assertSame(
            'email@test.com',
            $user->getUserIdentifier(),
        );
    }

    public function testEraseCredentials(): void
    {
        $user = new User();

        self::expectNotToPerformAssertions();

        /** @phpstan-ignore-next-line */
        $user->eraseCredentials();
    }

    public function testEventTypes(): void
    {
        $user = new User();

        self::assertCount(0, $user->getEventTypes());

        $eventTypeMock = $this->createMock(EventType::class);
        $user->addEventType($eventTypeMock);

        self::assertCount(1, $user->getEventTypes());
        self::assertSame(
            $user->getEventTypes()->first(),
            $eventTypeMock,
        );

        $eventTypeMock
            ->method('getHost')
            ->willReturn($user);

        $user->removeEventType($eventTypeMock);
        self::assertCount(0, $user->getEventTypes());
    }

    public function testUnavailabilities(): void
    {
        $user = new User();

        self::assertCount(0, $user->getUnavailabilities());

        $unavailabilityMock = $this->createMock(Unavailability::class);
        $user->addUnavailability($unavailabilityMock);

        self::assertCount(1, $user->getUnavailabilities());
        self::assertSame(
            $user->getUnavailabilities()->first(),
            $unavailabilityMock,
        );

        $unavailabilityMock
            ->method('getUser')
            ->willReturn($user);

        $user->removeUnavailability($unavailabilityMock);
        self::assertCount(0, $user->getUnavailabilities());
    }

    public function testAvailabilities(): void
    {
        $user = new User();

        self::assertCount(0, $user->getAvailabilities());

        $availabilityMock = $this->createMock(Availability::class);
        $user->addAvailability($availabilityMock);

        self::assertCount(1, $user->getAvailabilities());
        self::assertSame(
            $user->getAvailabilities()->first(),
            $availabilityMock,
        );

        $availabilityMock
            ->method('getUser')
            ->willReturn($user);

        $user->removeAvailability($availabilityMock);
        self::assertCount(0, $user->getAvailabilities());
    }

    public function testCalDavAuths(): void
    {
        $user = new User();

        self::assertCount(0, $user->getCalDavAuths());

        $calDavAuthMock = $this->createMock(CalDavAuth::class);
        $user->addCalDavAuth($calDavAuthMock);

        self::assertCount(1, $user->getCalDavAuths());
        self::assertSame(
            $user->getCalDavAuths()->first(),
            $calDavAuthMock,
        );

        $calDavAuthMock
            ->method('getUser')
            ->willReturn($user);

        $user->removeCalDavAuth($calDavAuthMock);
        self::assertCount(0, $user->getCalDavAuths());
    }

    public function testEnabled(): void
    {
        $user = new User();
        self::assertFalse($user->isEnabled());
        $user->setEnabled(true);
        self::assertTrue($user->isEnabled());
        $user->setEnabled(false);
        self::assertFalse($user->isEnabled());
    }

    public function testCreatedAt(): void
    {
        $dateTimeMock = $this->createMock(DateTimeImmutable::class);

        $user = new User();
        $user->setCreatedAt($dateTimeMock);

        self::assertSame(
            $dateTimeMock,
            $user->getCreatedAt(),
        );
    }

    public function testUpdatedAt(): void
    {
        $dateTimeMock = $this->createMock(DateTimeImmutable::class);

        $user = new User();
        $user->setUpdatedAt($dateTimeMock);

        self::assertSame(
            $dateTimeMock,
            $user->getUpdatedAt(),
        );
    }

    public function testSetCreatedAtValue(): void
    {
        $user = new User();
        $user->setCreatedAtValue();

        /** @phpstan-ignore-next-line */
        self::assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testSetUpdatedAtValue(): void
    {
        $user = new User();
        $user->setUpdatedAtValue();

        /** @phpstan-ignore-next-line */
        self::assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt());
    }

    public function testPasswodResetToken(): void
    {
        $user = new User();
        $user->setPasswordResetToken('token');
        self::assertSame('token', $user->getPasswordResetToken());
    }
}
