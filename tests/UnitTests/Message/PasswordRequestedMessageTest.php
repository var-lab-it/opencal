<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Message;

use App\Message\PasswordRequestedMessage;
use PHPUnit\Framework\TestCase;

class PasswordRequestedMessageTest extends TestCase
{
    public function testConstructor(): void
    {
        $message = new PasswordRequestedMessage(123);

        self::assertSame(123, $message->getUserId());
    }
}
