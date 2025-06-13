<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CalDavAuth;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsController]
class CreateCalDavAuthController extends AbstractController
{
    public function __invoke(CalDavAuth $data): CalDavAuth
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $data->setUser($user);

        return $data;
    }
}
