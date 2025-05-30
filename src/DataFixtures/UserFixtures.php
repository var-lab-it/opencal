<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'givenName'  => 'John',
                'familyName' => 'Doe',
                'email'      => 'user@example.tld',
            ],
            [
                'givenName'  => 'Jane',
                'familyName' => 'Smith',
                'email'      => 'jane.smith@example.tld',
            ],
            [
                'givenName'  => 'Alice',
                'familyName' => 'Johnson',
                'email'      => 'alice.johnson@example.tld',
            ],
            [
                'givenName'  => 'Bob',
                'familyName' => 'Brown',
                'email'      => 'bob.brown@example.tld',
            ],
            [
                'givenName'  => 'Charlie',
                'familyName' => 'Davis',
                'email'      => 'charlie.davis@example.tld',
            ],
            [
                'givenName'  => 'Emily',
                'familyName' => 'Wilson',
                'email'      => 'emily.wilson@example.tld',
            ],
            [
                'givenName'  => 'Frank',
                'familyName' => 'Miller',
                'email'      => 'frank.miller@example.tld',
            ],
            [
                'givenName'  => 'Grace',
                'familyName' => 'Taylor',
                'email'      => 'grace.taylor@example.tld',
            ],
            [
                'givenName'  => 'Henry',
                'familyName' => 'Anderson',
                'email'      => 'henry.anderson@example.tld',
            ],
            [
                'givenName'  => 'Isabella',
                'familyName' => 'Thomas',
                'email'      => 'isabella.thomas@example.tld',
            ],
        ];

        foreach ($usersData as $index => $data) {
            $user = new User();

            $user
                ->setGivenName($data['givenName'])
                ->setFamilyName($data['familyName'])
                ->setEmail($data['email'])
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setEnabled(true);
            $manager->persist($user);
            $this->addReference('user' . ($index + 1), $user);
        }

        $manager->flush();
    }
}
