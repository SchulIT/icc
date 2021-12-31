<?php

namespace App\Command;

use App\Rooms\Status\ServiceCenterRoomStatusHelper;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("*\/15 * * * *")
 */
class UpdateRoomStatusCommand extends Command {

    private ServiceCenterRoomStatusHelper $statusHelper;

    public function __construct(ServiceCenterRoomStatusHelper $roomStatusHelper, string $name = null) {
        parent::__construct($name);

        $this->statusHelper = $roomStatusHelper;
    }

    public function configure() {
        $this->setName('app:room:status:update')
            ->setDescription('Updates the room status from ServiceCenter (if enabled).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $this->statusHelper->retrieveFromRemote();

        $style->success('Status updated.');
        return 0;
    }
}