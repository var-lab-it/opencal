<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Command;

use App\Command\SyncCalDavCommand;
use App\Message\SyncCalDavMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SyncCalDavCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $messageBusMock = $this->createMock(MessageBusInterface::class);
        $messageBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(new SyncCalDavMessage()));

        $cmd = new SyncCalDavCommand($messageBusMock);

        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');
        $result   = $method->invoke(
            $cmd,
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );

        self::assertSame(0, $result);
    }
}
