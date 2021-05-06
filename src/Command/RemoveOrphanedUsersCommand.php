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

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);

        $this->userRepository = $userRepository;
    }

    public function configure() {
        parent::configure();

        $this
            ->setName('app:user:remove_orphaned')
            ->setDescription('Removes student, parents or teachers without any linked entity.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $count = $this->userRepository->removeOrphaned();

        $style->success(sprintf('Removed %d orphaned user(s).', $count));

        return 0;
    }
}