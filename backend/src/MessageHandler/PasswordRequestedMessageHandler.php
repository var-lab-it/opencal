<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\PasswordRequestedMessage;
use App\Repository\UserRepository;
use App\User\UserMailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PasswordRequestedMessageHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserMailService $userMailService,
    ) {
    }

    public function __invoke(PasswordRequestedMessage $message): void
    {
        $user = $this->userRepository->find($message->getUserId());

        if (!$user instanceof User) {
            return;
        }

        $this->userMailService->sendPasswordResetEmail($user);
    }
}
