<?php

namespace App\Command;

use App\Messenger\RunIntegrityCheckMessage;
use App\Repository\StudentRepositoryInterface;
use App\Section\SectionResolverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 1 * * *')]
#[AsCommand('app:book:integrity_check:queue', description: 'Veranlasst einen (asynchronen) Integritätscheck.')]
readonly class RunIntegrityCheckCommand {
    public function __construct(private MessageBusInterface $messageBus, private StudentRepositoryInterface $studentRepository,
                                private SectionResolverInterface $sectionResolver) { }

    public function __invoke(SymfonyStyle $io, OutputInterface $output): int {
        $section = $this->sectionResolver->getCurrentSection();

        if($section === null) {
            $io->error('Es gibt aktuell kein Schuljahresabschnitt zu prüfen');
            return Command::FAILURE;
        }

        foreach($this->studentRepository->findAllBySection($section) as $student) {
            $message = new RunIntegrityCheckMessage($student->getId(), $section->getStart(), $section->getEnd());
            $this->messageBus->dispatch($message);
        }

        return Command::SUCCESS;
    }
}