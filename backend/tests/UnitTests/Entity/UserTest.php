<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail('test@unit.tld');

        self::assertSame(
            'test@unit.tld',
            $user->getEmail(),
        );
    }
}
