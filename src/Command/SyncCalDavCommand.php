<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\SyncCalDavMessage;
use Safe\DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'sync:caldav',
    description: 'Syncs the caldav calendars with the database',
)]
class SyncCalDavCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = new DateTimeImmutable();
        $output->writeln('Trigger calendar sync at ' . $start->format('Y-m-d H:i:s'));

        $this->messageBus->dispatch(new SyncCalDavMessage());

        return self::SUCCESS;
    }
}
