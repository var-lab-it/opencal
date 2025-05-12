<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('transport')]
final class EventCanceledMessage
{
    public function __construct(
        private readonly int $eventId,
    ) {
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }
}
