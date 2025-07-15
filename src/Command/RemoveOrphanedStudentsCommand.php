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
readonly class RemoveOrphanedStudentsCommand {

    public function __construct(private StudentRepositoryInterface $studentRepository) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $count = $this->studentRepository->removeOrphaned();

        $style->success(sprintf('%s Lernende gelöscht', $count));

        return Command::SUCCESS;
    }
}