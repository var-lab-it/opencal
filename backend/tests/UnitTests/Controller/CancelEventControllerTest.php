<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Controller;

use App\Controller\CancelEventController;
use App\Entity\Event;
use App\Message\SyncCalDavMessage;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class CancelEventControllerTest extends TestCase
{
    use MatchesSnapshots;

    private MessageBusInterface&MockObject $messageBusMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Request&MockObject $requestMock;

    protected function setUp(): void
    {
        $this->messageBusMock    = $this->createMock(MessageBusInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->requestMock       = $this->createMock(Request::class);
    }

    public function testCancelEventWithInvalidHash(): void
    {
        $this->requestMock
            ->method('getContent')
            ->willReturn('{"cancellationHash": "invalid-hash"}');

        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getCancellationHash')
            ->willReturn('another-invalid-hash');

        self::expectException(BadRequestHttpException::class);
        self::expectExceptionMessage('Invalid cancellation hash');

        $controller = new CancelEventController(
            $this->messageBusMock,
            $this->entityManagerMock,
        );

        $controller->__invoke($eventMock, $this->requestMock);
    }

    public function testCancelEventWithIsAlreadyCancelled(): void
    {
        $this->requestMock
            ->method('getContent')
            ->willReturn('{"cancellationHash": "valid-hash"}');

        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getCancellationHash')
            ->willReturn('valid-hash');
        $eventMock
            ->method('isCancelledByAttendee')
            ->willReturn(true);

        self::expectException(BadRequestHttpException::class);
        self::expectExceptionMessage('Event already canceled by attendee');

        $controller = new CancelEventController(
            $this->messageBusMock,
            $this->entityManagerMock,
        );

        $controller->__invoke($eventMock, $this->requestMock);
    }

    public function testCancelEventSucceeds(): void
    {
        $this->requestMock
            ->method('getContent')
            ->willReturn('{"cancellationHash": "valid-hash"}');

        $this->messageBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(new SyncCalDavMessage()));
        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist');
        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getCancellationHash')
            ->willReturn('valid-hash');
        $eventMock
            ->method('isCancelledByAttendee')
            ->willReturn(false);
        $eventMock
            ->expects($this->once())
            ->method('setCanceledByAttendee');

        $controller = new CancelEventController(
            $this->messageBusMock,
            $this->entityManagerMock,
        );

        $result = $controller->__invoke($eventMock, $this->requestMock);

        self::assertMatchesJsonSnapshot($result->getContent());
        self::assertSame(200, $result->getStatusCode());
    }
}
