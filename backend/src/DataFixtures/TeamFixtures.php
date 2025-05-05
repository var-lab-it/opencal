<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TeamFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        $data = [
            [
                'name'     => 'My Team 1',
                'members'  => [
                    $this->getReference('user1', User::class),
                    $this->getReference('user2', User::class),
                    $this->getReference('user3', User::class),
                    $this->getReference('user4', User::class),
                ],
                'managers' => [
                    $this->getReference('user1', User::class),
                    $this->getReference('user2', User::class),
                ],
            ],
            [
                'name'     => 'My Team 2',
                'members'  => [
                    $this->getReference('user1', User::class),
                    $this->getReference('user4', User::class),
                    $this->getReference('user5', User::class),
                    $this->getReference('user7', User::class),
                ],
                'managers' => [
                    $this->getReference('user2', User::class),
                ],
            ],
            [
                'name'     => 'My Team 3',
                'members'  => [
                    $this->getReference('user5', User::class),
                    $this->getReference('user6', User::class),
                    $this->getReference('user7', User::class),
                    $this->getReference('user8', User::class),
                    $this->getReference('user9', User::class),
                ],
                'managers' => [
                    $this->getReference('user4', User::class),
                ],
            ],
        ];

        foreach ($data as $item) {
            $team = new Team();
            $team
                ->setName($item['name'])
                ->setSlug(
                    $slugger
                        ->slug($item['name'], '-', 'en')
                        ->lower()
                        ->toString(),
                );

            foreach ($item['members'] as $user) {
                $team->addMember($user);
            }

            foreach ($item['managers'] as $user) {
                $team->addManager($user);
            }

            $manager->persist($team);
        }

        $manager->flush();
    }
}
