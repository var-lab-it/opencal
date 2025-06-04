<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Doctrine;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Doctrine\CalDavAuthExtension;
use App\Entity\CalDavAuth;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class CalDavAuthExtensionTest extends TestCase
{
    private Security&MockObject $securityMock;
    private QueryBuilder&MockObject $queryBuilderMock;

    private CalDavAuthExtension $eventTypeExtension;

    protected function setUp(): void
    {
        $this->securityMock     = $this->createMock(Security::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);

        $this->eventTypeExtension = new CalDavAuthExtension($this->securityMock);
    }

    public function testApplyToCollectionAddsWhereConditionForCalDavAuth(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->method('getId')
            ->willReturn(42);

        $this->securityMock
            ->method('getUser')
            ->willReturn($user);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['e']);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with('e.user = :current_user');

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('current_user', 42);

        $this->eventTypeExtension->applyToCollection(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            CalDavAuth::class,
            $this->createMock(Operation::class),
            [],
        );
    }

    public function testApplyToItemAddsWhereConditionForCalDavAuth(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->method('getId')
            ->willReturn(42);

        $this->securityMock
            ->method('getUser')
            ->willReturn($user);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['e']);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with('e.user = :current_user');

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('current_user', 42);

        $this->eventTypeExtension->applyToItem(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            CalDavAuth::class,
            ['id' => 1],
            $this->createMock(Operation::class),
        );
    }

    public function testAddWhereDoesNothingIfResourceClassIsNotCalDavAuth(): void
    {
        $this->securityMock
            ->method('getUser')
            ->willReturn(new User());

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere');

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('setParameter');

        $this->eventTypeExtension->applyToCollection(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            'OtherResourceClass', // @phpstan-ignore-line
        );
    }

    public function testAddWhereDoesNothingIfUserIsNotInstanceOfUser(): void
    {
        $this->securityMock
            ->method('getUser')
            ->willReturn(null);

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere');

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('setParameter');

        $this->eventTypeExtension->applyToCollection(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            CalDavAuth::class,
        );
    }
}
