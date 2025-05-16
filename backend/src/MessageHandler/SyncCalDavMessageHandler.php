<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\CalDavAuth;
use App\Entity\Event;
use App\Message\SyncCalDavMessage;
use App\Repository\CalDavAuthRepository;
use App\Repository\EventRepository;
use App\Service\CalDavService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Safe\DateTime;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncCalDavMessageHandler
{
    public function __construct(
        private readonly CalDavService $calDavService,
        private readonly CalDavAuthRepository $calDavAuthRepository,
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function __invoke(SyncCalDavMessage $message): void
    {
        $calDavAuths = $this->calDavAuthRepository
            ->findBy([
                'enabled' => true,
            ]);

        foreach ($calDavAuths as $calDavAuth) {
            $this->logger->info(\sprintf(
                '[caldav sync]: Start sync for caldav-auth-id %s',
                $calDavAuth->getId(),
            ));

            $eventsData = $this->calDavService->fetchEventsByAuth($calDavAuth);

            $this->logger->info(\sprintf(
                '[caldav sync]: Count fetched entries: %s',
                \count($eventsData),
            ));

            $addedETags = [];

            /** @var array{day: string, startTime: string, endTime: string, etag: string} $item */
            foreach ($eventsData as $item) {
                $event = $this->eventRepository->findOneBySyncHashAndUser($item['etag'], $calDavAuth->getUser());

                if (!$event instanceof Event) {
                    $event = new Event();
                }

                $event
                    ->setDay(new DateTime($item['day']))
                    ->setStartTime(new DateTime($item['startTime']))
                    ->setEndTime(new DateTime($item['endTime']))
                    ->setSyncHash($item['etag'])
                    ->setCalDavAuth($calDavAuth);

                $this->entityManager->persist($event);

                $addedETags[] = $item['etag'];

                $this->logger->info(\sprintf(
                    '[caldav sync]: Entry added: etag %s',
                    $event->getSyncHash(),
                ));
            }

            $this->deleteDeprecatedEvents($addedETags, $calDavAuth);

            $this->entityManager->flush();

            $this->logger->info(\sprintf(
                '[caldav sync]: Finished for caldav-auth-id %s',
                $calDavAuth->getId(),
            ));
        }
    }

    /** @param array<string> $eTags */
    public function deleteDeprecatedEvents(array $eTags, CalDavAuth $calDavAuth): void
    {
        $eventsToDelete = $this->eventRepository->findAllByCalDavAuthNotInETagList($calDavAuth, $eTags);

        foreach ($eventsToDelete as $event) {
            $this->entityManager->remove($event);

            $this->logger->info(\sprintf(
                '[caldav sync]: Deprecated event deleted: etag %s',
                $event->getSyncHash(),
            ));
        }

        $this->entityManager->flush();
    }
}
