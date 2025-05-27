<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManagerMock;
    private UserRepository&MockObject $userRepositoryMock;
    private UserPasswordHasherInterface&MockObject $userPasswordHasherMock;

    protected function setUp(): void
    {
        $this->entityManagerMock      = $this->createMock(EntityManagerInterface::class);
        $this->userRepositoryMock     = $this->createMock(UserRepository::class);
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testCreateUser(): void
    {
        $service = $this->getService();

        $user = $service->createUser();

        self::assertSame(
            [
                User::ROLE_USER,
            ],
            $user->getRoles(),
        );
    }

    public function testSaveUser(): void
    {
        $userMock = $this->createMock(User::class);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($userMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $service = $this->getService();
        $service->saveUser($userMock);
    }

    public function testIsEmailUsedTrue(): void
    {
        $userMock = $this->createMock(User::class);

        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn($userMock)
            ->with('test@email.tld');

        $service = $this->getService();
        $result  = $service->isEmailUsed('test@email.tld');

        self::assertTrue($result);
    }

    public function testIsEmailUsedFalse(): void
    {
        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn(null)
            ->with('test@email.tld');

        $service = $this->getService();
        $result  = $service->isEmailUsed('test@email.tld');

        self::assertFalse($result);
    }

    public function testGeneratePasswordResetToken(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock
            ->expects(self::once())
            ->method('setPasswordResetToken');

        $service = $this->getService();
        $service->generatePasswordResetToken($userMock);
    }

    public function testSetPassword(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock
            ->expects(self::once())
            ->method('setPassword');

        $this->userPasswordHasherMock
            ->expects(self::once())
            ->method('hashPassword')
            ->with($userMock, 'password');

        $service = $this->getService();
        $service->setPassword($userMock, 'password');
    }

    private function getService(): UserService
    {
        return new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock,
            $this->userPasswordHasherMock,
        );
    }
}
