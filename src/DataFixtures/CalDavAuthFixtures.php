<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CalDavAuth;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CalDavAuthFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $user1      = $this->getReference('user1', User::class);
        $calDavAuth = new CalDavAuth();
        $calDavAuth
            ->setEnabled(true)
            ->setUsername('dev')
            ->setPassword('dev')
            ->setBaseUri('http://radicale:5232/dev/example/')
            ->setUser($user1);
        $manager->persist($calDavAuth);

        $user2      = $this->getReference('user2', User::class);
        $calDavAuth = new CalDavAuth();
        $calDavAuth
            ->setEnabled(true)
            ->setUsername('user2')
            ->setPassword('password')
            ->setBaseUri('http://caldav.calendar')
            ->setUser($user2);
        $manager->persist($calDavAuth);

        $manager->flush();
    }
}
