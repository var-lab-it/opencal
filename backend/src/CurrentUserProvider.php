<?php

declare(strict_types=1);

namespace App;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/** @implements ProviderInterface<User> */
class CurrentUserProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        return $user;
    }
}
