<?php

declare(strict_types=1);

namespace App\Controller;

use App\ApiResource\ResetPassword;
use App\Entity\User;
use App\Repository\UserRepository;
use App\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class ResetPasswordController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserService $userService,
    ) {
    }

    public function __invoke(ResetPassword $resetPassword): Response
    {
        $email = $resetPassword->getEmail();

        $user = $this->userRepository->findOneByEmail($email);

        if (!$user instanceof User) {
            throw $this->createNotFoundException(\sprintf(
                'User not found for email "%s"',
                $email,
            ));
        }

        if ($resetPassword->getToken() !== $user->getPasswordResetToken()) {
            throw new BadRequestHttpException(\sprintf(
                'Invalid token.',
            ));
        }

        $user = $this->userService->setPassword($user, $resetPassword->getPassword());
        $this->userService->saveUser($user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
