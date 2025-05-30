<?php

declare(strict_types=1);

namespace App\Command;

use App\User\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'opencal:user:create',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new user')
            ->setHelp('This command allows you to create a new user.')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'A email address for the new user (must be unique)',
            )
            ->addArgument(
                'given_name',
                InputArgument::REQUIRED,
                'The given name of the new user.',
            )
            ->addArgument(
                'family_name',
                InputArgument::REQUIRED,
                'The family name of the new user.',
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'Password for the new user (the user needs to set a new password during next login)',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $userEmail */
        $userEmail = $input->getArgument('email');
        /** @var string $givenName */
        $givenName = $input->getArgument('given_name');
        /** @var string $familyName */
        $familyName = $input->getArgument('family_name');
        /** @var ?string $userPassword */
        $userPassword = $input->getArgument('password');

        if (false === \filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $io->error('The email is not valid. Please provide a valid email address.');

            return Command::FAILURE;
        }

        if (null !== $userPassword && \strlen($userPassword) < 5) {
            $io->error('The password must be at least 5 characters long.');

            return Command::FAILURE;
        }

        if ('' === $givenName || '' === $familyName) {
            $io->error('Given name and family name are both required.');

            return Command::FAILURE;
        }

        if (true === $this->userService->isEmailUsed($userEmail)) {
            $io->error(\sprintf(
                'The email address "%s" is already used by another user.',
                $userEmail,
            ));

            return Command::FAILURE;
        }

        $user = $this->userService->createUser();
        $user
            ->setGivenName($givenName)
            ->setFamilyName($familyName)
            ->setEmail($userEmail);

        if (null === $userPassword) {
            $user
                ->setPassword(\uniqid()); // requires password reset by the user
        } else {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $userPassword));
        }

        $this->userService->saveUser($user);

        $io->success(\sprintf(
            'A new user account for "%s" has been created.',
            $givenName,
        ));

        return Command::SUCCESS;
    }
}
