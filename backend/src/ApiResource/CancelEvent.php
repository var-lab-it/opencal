<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\CancelEventController;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/events/{id}/cancel',
            controller: CancelEventController::class,
            openapi: new Model\Operation(
                description: 'Cancels a specific event. Requires a valid cancellationHash ' .
                'to authorize the cancellation.',
                requestBody: new Model\RequestBody(
                    description: 'The cancellation hash',
                ),
            ),
        ),
    ],
)]
class CancelEvent
{
    private string $cancellationHash;

    public function getCancellationHash(): string
    {
        return $this->cancellationHash;
    }

    public function setCancellationHash(string $cancellationHash): void
    {
        $this->cancellationHash = $cancellationHash;
    }
}
