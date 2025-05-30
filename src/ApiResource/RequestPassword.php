<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\RequestPasswordController;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password/request',
            controller: RequestPasswordController::class,
            openapi: new Model\Operation(
                responses: [
                    '204' => [
                        'description' => 'No content. Password reset request accepted.',
                    ],
                ],
            ),
            read: false,
            write: false,
        ),
    ],
)]
class RequestPassword
{
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
