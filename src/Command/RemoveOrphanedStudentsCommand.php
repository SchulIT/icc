<?php

namespace App\Command;

use App\Repository\StudentRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("@daily")
 */
class RemoveOrphanedStudentsCommand extends Command {

    protected static $defaultName = 'app:students:remove_orphaned';

    public function __construct(private StudentRepositoryInterface $studentRepository, string $name = null) {
        parent::__construct($name);
    }

    public function configure() {
        parent::configure();

        $this->setDescription('Removes students without any linked grade or section.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $count = $this->studentRepository->removeOrphaned();

        $style->success(sprintf('Removed %d orphaned student(s).', $count));

        return 0;
    }
}