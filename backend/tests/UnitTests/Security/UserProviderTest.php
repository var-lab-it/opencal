<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Security;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProviderTest extends TestCase
{
    private UserRepository&MockObject $userRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->entityManagerMock  = $this->createMock(EntityManagerInterface::class);
    }

    public function testUpgradePasswordInvalidUserClass(): void
    {
        $userMock = $this->createMock(PasswordAuthenticatedUserInterface::class);

        $this->entityManagerMock
            ->expects(self::never())
            ->method('persist');
        $this->entityManagerMock
            ->expects(self::never())
            ->method('flush');

        $provider = $this->getProvider();
        $provider->upgradePassword($userMock, 'test');
    }

    public function testUpgradePasswordSucceeds(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock
            ->expects(self::once())
            ->method('setPassword');

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist');
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $provider = $this->getProvider();
        $provider->upgradePassword($userMock, 'test');
    }

    public function testRefreshUserInvalidUserClass(): void
    {
        $userMock = $this->createMock(UserInterface::class);

        self::expectException(UnsupportedUserException::class);
        self::expectExceptionMessage('Invalid user class.');

        $provider = $this->getProvider();
        $provider->refreshUser($userMock);
    }

    public function testRefreshUserWithoutFetchedUser(): void
    {
        $userMock = $this->createMock(User::class);

        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage('User not found.');

        $provider = $this->getProvider();
        $provider->refreshUser($userMock);
    }

    public function testRefreshUserFetchedUserDisabled(): void
    {
        $userMock = $this->createMock(User::class);

        $fetchedUserMock = $this->createMock(User::class);
        $fetchedUserMock
            ->method('isEnabled')
            ->willReturn(false);

        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn($fetchedUserMock);

        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage('User is disabled.');

        $provider = $this->getProvider();
        $provider->refreshUser($userMock);
    }

    public function testRefreshUserSucceeds(): void
    {
        $userMock = $this->createMock(User::class);

        $fetchedUserMock = $this->createMock(User::class);
        $fetchedUserMock
            ->method('isEnabled')
            ->willReturn(true);

        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn($fetchedUserMock);

        $provider = $this->getProvider();
        $result   = $provider->refreshUser($userMock);

        self::assertSame(
            $fetchedUserMock,
            $result,
        );
    }

    public function testSupportsClass(): void
    {
        $provider = $this->getProvider();
        $result   = $provider->supportsClass(User::class);
        self::assertTrue($result);

        $result = $provider->supportsClass(Event::class);
        self::assertFalse($result);
    }

    private function getProvider(): UserProvider
    {
        return new UserProvider(
            $this->userRepositoryMock,
            $this->entityManagerMock,
        );
    }
}
