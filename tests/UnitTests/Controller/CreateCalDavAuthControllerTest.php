<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Controller;

use App\Controller\CreateCalDavAuthController;
use App\Entity\CalDavAuth;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CreateCalDavAuthControllerTest extends TestCase
{
    public function testInvokeUserNotFound(): void
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock
            ->expects(self::once())
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);

        $tokenStorageMock = $this->createMock(TokenStorage::class);
        $containerMock
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($tokenStorageMock);

        $controller = new CreateCalDavAuthController();
        $controller->setContainer($containerMock);

        self::expectException(AccessDeniedHttpException::class);

        $calDavAuth = new CalDavAuth();
        $controller->__invoke($calDavAuth);
    }

    public function testInvokeUserFound(): void
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock
            ->expects(self::once())
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);

        $tokenStorageMock = $this->createMock(TokenStorage::class);
        $containerMock
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($tokenStorageMock);

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenStorageMock
            ->method('getToken')
            ->willReturn($tokenMock);

        $userMock = $this->createMock(User::class);
        $tokenMock
            ->method('getUser')
            ->willReturn($userMock);

        $controller = new CreateCalDavAuthController();
        $controller->setContainer($containerMock);

        $calDavAuth = new CalDavAuth();
        $result     = $controller->__invoke($calDavAuth);

        self::assertEquals(
            $userMock,
            $result->getUser(),
        );
    }
}
