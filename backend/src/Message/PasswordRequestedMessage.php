<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class PasswordRequestedMessage
{
    public function __construct(
        private int $userId,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
