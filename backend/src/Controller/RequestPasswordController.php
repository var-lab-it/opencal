<?php

declare(strict_types=1);

namespace App\Controller;

use App\ApiResource\RequestPassword;
use App\Entity\User;
use App\Message\PasswordRequestedMessage;
use App\Repository\UserRepository;
use App\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsController]
class RequestPasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(RequestPassword $requestPassword): Response
    {
        $user = $this->userRepository->findOneByEmail($requestPassword->getEmail());

        if (!$user instanceof User) {
            throw $this->createNotFoundException(\sprintf(
                'User with email "%s" not found.',
                $requestPassword->getEmail(),
            ));
        }

        $this->userService->generatePasswordResetToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->messageBus->dispatch(
            new PasswordRequestedMessage(
                $user->getId(),
            ),
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
