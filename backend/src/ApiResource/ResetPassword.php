<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\ResetPasswordController;
use Symfony\Component\HttpFoundation\Response;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password/reset',
            controller: ResetPasswordController::class,
            openapi: new Model\Operation(
                responses: [
                    Response::HTTP_NO_CONTENT => [
                        'description' => 'No content. Password reset is done.',
                    ],
                ],
            ),
        ),
    ],
)]
class ResetPassword
{
    private string $token;

    private string $email;

    private string $password;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
