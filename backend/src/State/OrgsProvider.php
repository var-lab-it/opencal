<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/** @implements ProviderInterface<Team> */
class OrgsProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $user->getTeams();
        }

        return [];
    }
}
