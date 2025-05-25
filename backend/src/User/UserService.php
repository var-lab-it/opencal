<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
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
}
