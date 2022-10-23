<?php

namespace App\Command;

use App\Rooms\Status\ServiceCenterRoomStatusHelper;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob('*\/15 * * * *')]
#[AsCommand('app:room:status:update', 'Updates the room status from ServiceCenter (if enabled).')]
class UpdateRoomStatusCommand extends Command {

    public function __construct(private ServiceCenterRoomStatusHelper $statusHelper, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $this->statusHelper->retrieveFromRemote();

        $style->success('Status updated.');
        return 0;
    }
}