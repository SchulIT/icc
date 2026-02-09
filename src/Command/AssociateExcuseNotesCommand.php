<?php

namespace App\Command;

use App\Book\Excuse\ExcuseNoteAssociator;
use App\Entity\ExcuseNote;
use App\Repository\ExcuseNoteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:book:excuse_notes:associate', description: 'Verknüpft Entschuldigungen mit den zugehörigen Anwesenheiten')]
#[AsCronTask('*/15 * * * *')]
readonly class AssociateExcuseNotesCommand {

    public function __construct(
        private ExcuseNoteAssociator          $associator,
        private ExcuseNoteRepositoryInterface $excuseNoteRepository,
        private EntityManagerInterface        $entityManager
    ) {

    }

    public function __invoke(
        SymfonyStyle $io,
    ): int {
        $io->section('Verknüpfe Entschuldigungen');

        $excuses = $this->excuseNoteRepository->findAll();
        $progress = new ProgressBar($io, count($excuses));

        foreach($excuses as $excuse) {
            $entity = $this->entityManager->getRepository(ExcuseNote::class)->findOneBy(['id' => $excuse->getId()]);

            $this->associator->associateExcuseNote($entity);
            $this->entityManager->flush();
            $progress->advance();

            $this->entityManager->clear();
        }

        $progress->finish();

        $io->success('Alle Entschuldigungen verknüpft');
        return Command::SUCCESS;
    }
}