<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\EventListener\Doctrine;

use App\Entity\Event;
use App\EventListener\Doctrine\EventPrePersistEventListener;
use PHPUnit\Framework\TestCase;

class EventPrePersistEventListenerTest extends TestCase
{
    public function testPrePersist(): void
    {
        $handler = new EventPrePersistEventListener();

        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->expects($this->once())
            ->method('setCancellationHash');

        $handler->prePersist($eventMock);
    }
}
