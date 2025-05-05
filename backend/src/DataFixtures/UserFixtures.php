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
        $user1 = new User();
        $user1
            ->setGivenName('John')
            ->setFamilyName('Doe')
            ->setEmail('user@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user1, 'password'));
        $manager->persist($user1);
        $this->addReference('user1', $user1);

        $user2 = new User();
        $user2
            ->setGivenName('Jane')
            ->setFamilyName('Smith')
            ->setEmail('jane.smith@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user2, 'password'));
        $manager->persist($user2);
        $this->addReference('user2', $user2);

        $user3 = new User();
        $user3
            ->setGivenName('Alice')
            ->setFamilyName('Johnson')
            ->setEmail('alice.johnson@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user3, 'password'));
        $manager->persist($user3);
        $this->addReference('user3', $user3);

        $user4 = new User();
        $user4
            ->setGivenName('Bob')
            ->setFamilyName('Brown')
            ->setEmail('bob.brown@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user4, 'password'));
        $manager->persist($user4);
        $this->addReference('user4', $user4);

        $user5 = new User();
        $user5
            ->setGivenName('Charlie')
            ->setFamilyName('Davis')
            ->setEmail('charlie.davis@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user5, 'password'));
        $manager->persist($user5);
        $this->addReference('user5', $user5);

        $user6 = new User();
        $user6
            ->setGivenName('Emily')
            ->setFamilyName('Wilson')
            ->setEmail('emily.wilson@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user6, 'password'));
        $manager->persist($user6);
        $this->addReference('user6', $user6);

        $user7 = new User();
        $user7
            ->setGivenName('Frank')
            ->setFamilyName('Miller')
            ->setEmail('frank.miller@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user7, 'password'));
        $manager->persist($user7);
        $this->addReference('user7', $user7);

        $user8 = new User();
        $user8
            ->setGivenName('Grace')
            ->setFamilyName('Taylor')
            ->setEmail('grace.taylor@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user8, 'password'));
        $manager->persist($user8);
        $this->addReference('user8', $user8);

        $user9 = new User();
        $user9
            ->setGivenName('Henry')
            ->setFamilyName('Anderson')
            ->setEmail('henry.anderson@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user9, 'password'));
        $manager->persist($user9);
        $this->addReference('user9', $user9);

        $user10 = new User();
        $user10
            ->setGivenName('Isabella')
            ->setFamilyName('Thomas')
            ->setEmail('isabella.thomas@example.tld')
            ->setPassword($this->passwordHasher->hashPassword($user10, 'password'));
        $manager->persist($user10);
        $this->addReference('user10', $user10);

        $manager->flush();
    }
}
