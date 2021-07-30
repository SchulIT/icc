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

    private $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, string $name = null) {
        parent::__construct($name);

        $this->studentRepository = $studentRepository;
    }

    public function configure() {
        parent::configure();

        $this
            ->setName('app:students:remove_orphaned')
            ->setDescription('Removes students without any linked grade or section.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $count = $this->studentRepository->removeOrphaned();

        $style->success(sprintf('Removed %d orphaned student(s).', $count));

        return 0;
    }
}