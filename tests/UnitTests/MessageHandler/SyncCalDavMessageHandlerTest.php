<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\MessageHandler;

use App\CalDav\CalDavService;
use App\Entity\CalDavAuth;
use App\Message\SyncCalDavMessage;
use App\MessageHandler\SyncCalDavMessageHandler;
use App\Repository\CalDavAuthRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SyncCalDavMessageHandlerTest extends TestCase
{
    private CalDavService&MockObject $calDavService;
    private CalDavAuthRepository&MockObject $calDavAuthRepository;
    private EventRepository&MockObject $eventRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->calDavService        = $this->createMock(CalDavService::class);
        $this->calDavAuthRepository = $this->createMock(CalDavAuthRepository::class);
        $this->eventRepository      = $this->createMock(EventRepository::class);
        $this->entityManager        = $this->createMock(EntityManagerInterface::class);
        $this->logger               = $this->createMock(LoggerInterface::class);
    }

    public function testInvokeWithoutAuths(): void
    {
        $this->calDavAuthRepository
            ->method('findBy')
            ->willReturn([]);

        $this->entityManager
            ->expects($this->never())
            ->method('persist');
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $handler = new SyncCalDavMessageHandler(
            $this->calDavService,
            $this->calDavAuthRepository,
            $this->eventRepository,
            $this->entityManager,
            $this->logger,
        );

        $handler->__invoke(new SyncCalDavMessage());
    }

    public function testInvokeWithAuthsAndEventData(): void
    {
        $this->calDavAuthRepository
            ->method('findBy')
            ->willReturn([
                $this->createMock(CalDavAuth::class),
                $this->createMock(CalDavAuth::class),
            ]);

        $this->entityManager
            ->expects($this->exactly(4))
            ->method('persist');
        $this->entityManager
            ->expects($this->exactly(4))
            ->method('flush');

        $this->calDavService
            ->method('fetchEventsByAuth')
            ->willReturn([
                [
                    'day'       => '2021-01-01',
                    'startTime' => '10:00',
                    'endTime'   => '11:00',
                    'etag'      => '123hash',
                ],
                [
                    'day'       => '2021-01-01',
                    'startTime' => '10:00',
                    'endTime'   => '11:00',
                    'etag'      => '123hash',
                ],
            ]);

        $handler = new SyncCalDavMessageHandler(
            $this->calDavService,
            $this->calDavAuthRepository,
            $this->eventRepository,
            $this->entityManager,
            $this->logger,
        );

        $handler->__invoke(new SyncCalDavMessage());
    }
}
