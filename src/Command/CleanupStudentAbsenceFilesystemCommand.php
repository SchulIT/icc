<?php

namespace App\Command;

use App\Entity\StudentAbsenceAttachment;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:filesystem:student_absence:cleanup', description: 'Räumt den Ordner files/student_absence/ auf und synchronisiert ihn mit der Datenbank.')]
readonly class CleanupStudentAbsenceFilesystemCommand {
    public function __construct(private FilesystemOperator $student_absenceFilesystem,
                                private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output, #[Option('Nur prüfen und nichts löschen.', 'dry-run', 'd')] bool $dryRun = false): int {
        if($dryRun === true) {
            $style->info('Diese Operation wird als `dry-run` ausgeführt. Es wird nichts gelöscht.');
        }

        $style->section('Anhänge (Abwesenheitsmeldungen) aufräumen');
        $attachments = $this->em->getRepository(StudentAbsenceAttachment::class)->findAll();

        $style->section('Prüfe Anhänge in Datenbank...');
        foreach($attachments as $attachment) {
            $style->write(sprintf('Prüfe %s: ', $attachment->getPath()));

            if($this->student_absenceFilesystem->fileExists($attachment->getPath())) {
                $style->writeln('existiert. Unternehme nichts.');
            } else if($dryRun) {
                $style->writeln('existiert nicht. Unternehme nichts (dry-run).');
            } else {
                $style->writeln('existiert nicht. Lösche aus Datenbank.');
                $this->em->remove($attachment);
            }
        }

        $this->em->flush();

        $style->section('Lösche verwaiste Anhänge vom Dateisystem');

        foreach($this->student_absenceFilesystem->listContents('/') as $item) {
            if($item->path() === '.gitignore' || !$item instanceof FileAttributes) {
                continue;
            }

            $style->write(sprintf('Prüfe Datei %s: ', $item->path()));

            $chat = $this->em->getRepository(StudentAbsenceAttachment::class)->findOneBy(['path' => $item->path()]);

            if($chat === null) {
                if($dryRun) {
                    $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                } else {
                    $style->writeln('nicht in Datenbank vorhanden. Lösche Datei.');
                    $this->student_absenceFilesystem->delete($item->path());
                }
            } else {
                $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
            }
        }

        return Command::SUCCESS;
    }
}