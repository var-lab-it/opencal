<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Message\EventCanceledMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use function Safe\json_decode;

#[AsController]
class CancelEventController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Event $event, Request $request): JsonResponse
    {
        $requestContent = $request->getContent();

        /** @var array{cancellationHash: string} $json */
        $json = json_decode($requestContent, true);
        $hash = $json['cancellationHash'];

        if ($hash !== $event->getCancellationHash()) {
            throw new BadRequestHttpException('Invalid cancellation hash');
        }

        if (true === $event->isCancelledByAttendee()) {
            throw new BadRequestHttpException('Event already canceled by attendee');
        }

        $event
            ->setCanceledByAttendee(true);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $this->messageBus->dispatch(
            new EventCanceledMessage($event->getId()),
        );

        return new JsonResponse([
            'success' => 'true',
        ]);
    }
}
