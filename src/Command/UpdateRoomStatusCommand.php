<?php

namespace App\Command;

use App\Rooms\Status\ServiceCenterRoomStatusHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('*/15 * * * *')]
#[AsCommand('app:room:status:update', 'Aktualisiert den Raumstatus aus dem ServiceCenter (falls aktiviert)')]
readonly class UpdateRoomStatusCommand {

    public function __construct(private ServiceCenterRoomStatusHelper $statusHelper) {    }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $this->statusHelper->retrieveFromRemote();

        $style->success('Status aktualisiert');
        return Command::SUCCESS;
    }
}