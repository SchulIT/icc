<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("@daily")
 */
class RemoveOrphanedUsersCommand extends Command {

    protected static $defaultName = 'app:user:remove_orphaned';

    public function __construct(private UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
    }

    public function configure() {
        parent::configure();

        $this->setDescription('Removes student, parents or teachers without any linked entity.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $count = $this->userRepository->removeOrphaned();

        $style->success(sprintf('Removed %d orphaned user(s).', $count));

        return 0;
    }
}