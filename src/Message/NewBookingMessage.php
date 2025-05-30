<?php

declare(strict_types=1);

namespace App\Message;

final class NewBookingMessage
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
