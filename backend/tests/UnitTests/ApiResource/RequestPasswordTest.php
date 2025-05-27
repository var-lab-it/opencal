<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\ApiResource;

use App\ApiResource\RequestPassword;
use PHPUnit\Framework\TestCase;

class RequestPasswordTest extends TestCase
{
    public function testEmail(): void
    {
        $resource = new RequestPassword();
        $resource->setEmail('test@unit.tld');

        self::assertSame(
            'test@unit.tld',
            $resource->getEmail(),
        );
    }
}
