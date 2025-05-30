<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Controller;

use App\ApiResource\RequestPassword;
use App\Controller\RequestPasswordController;
use App\Entity\User;
use App\Message\PasswordRequestedMessage;
use App\Repository\UserRepository;
use App\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class RequestPasswordControllerTest extends TestCase
{
    private UserRepository&MockObject $userRepositoryMock;
    private UserService&MockObject $userServiceMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private MessageBusInterface&MockObject $messageBusMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->userServiceMock    = $this->createMock(UserService::class);
        $this->entityManagerMock  = $this->createMock(EntityManagerInterface::class);
        $this->messageBusMock     = $this->createMock(MessageBusInterface::class);
    }

    public function testInvokeUserNotFound(): void
    {
        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn(null);

        $requestPasswordMock = $this->createMock(RequestPassword::class);
        $requestPasswordMock
            ->method('getEmail')
            ->willReturn('email@domain.com');

        self::expectException(NotFoundHttpException::class);
        self::expectExceptionMessage('User with email "email@domain.com" not found.');

        $controller = $this->getController();
        $controller($requestPasswordMock);
    }

    public function testInvokeSucceeds(): void
    {
        $userMock = $this->createMock(User::class);

        $this->userRepositoryMock
            ->method('findOneByEmail')
            ->willReturn($userMock);

        $this->userServiceMock
            ->expects(self::once())
            ->method('generatePasswordResetToken')
            ->with($userMock);

        $requestPasswordMock = $this->createMock(RequestPassword::class);
        $requestPasswordMock
            ->method('getEmail')
            ->willReturn('email@domain.com');

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($userMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->messageBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope(new PasswordRequestedMessage(123)));

        $controller = $this->getController();
        $controller($requestPasswordMock);
    }

    private function getController(): RequestPasswordController
    {
        return new RequestPasswordController(
            $this->userRepositoryMock,
            $this->userServiceMock,
            $this->entityManagerMock,
            $this->messageBusMock,
        );
    }
}
