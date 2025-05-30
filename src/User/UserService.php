<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function createUser(): User
    {
        $user = new User();
        $user
            ->setEnabled(true)
            ->setRoles([User::ROLE_USER]);

        return $user;
    }

    public function saveUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function isEmailUsed(string $email): bool
    {
        $existingUser = $this->userRepository->findOneByEmail($email);

        return $existingUser instanceof User;
    }

    public function generatePasswordResetToken(User $user): string
    {
        $token = \hash('sha512', \random_bytes(32));

        $user->setPasswordResetToken($token);

        return $token;
    }

    public function setPassword(User $user, string $plainPassword): User
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        return $user;
    }
}
