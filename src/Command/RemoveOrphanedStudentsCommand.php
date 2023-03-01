<?php

namespace App\Command;

use App\Repository\StudentRepositoryInterface;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob('@daily')]
#[AsCommand('app:students:remove_orphaned', 'Löscht Lernende, die mit keinem Schulabschnitt verknüpft sind.')]
class RemoveOrphanedStudentsCommand extends Command {

    public function __construct(private readonly StudentRepositoryInterface $studentRepository, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $count = $this->studentRepository->removeOrphaned();

        $style->success(sprintf('%s Lernende gelöscht', $count));

        return 0;
    }
}