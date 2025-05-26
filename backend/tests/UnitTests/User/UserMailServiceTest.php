<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\User;

use App\Entity\User;
use App\Mail\MailService;
use App\User\UserMailService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailServiceTest extends TestCase
{
    private MailService&MockObject $mailServiceMock;
    private TranslatorInterface&MockObject $translatorMock;

    protected function setUp(): void
    {
        $this->mailServiceMock = $this->createMock(MailService::class);
        $this->translatorMock  = $this->createMock(TranslatorInterface::class);
    }

    public function testSendPasswordResetEmail(): void
    {
        $this->mailServiceMock
            ->expects(self::once())
            ->method('sendEmail');

        $userMock = $this->createMock(User::class);

        $service = $this->getService();
        $service->sendPasswordResetEmail($userMock);
    }

    private function getService(): UserMailService
    {
        return new UserMailService(
            $this->mailServiceMock,
            $this->translatorMock,
            'en_GB',
            'http://frontend.tld',
            true,
        );
    }
}
